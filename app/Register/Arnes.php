<?php

namespace App\Register;

use Exception;

use AfriCC\EPP\Client as EPPClient;

class ArnesEPPClient extends EPPClient
{
    /**
     * This is overriden because why?
     *
     * @return null
     * @throws Exception
     */
    public function connect()
    {
        if ($this->ssl) {
            $proto = 'ssl';

            $context = stream_context_create();
            stream_context_set_option($context, 'ssl', 'verify_peer', false);
            stream_context_set_option($context, 'ssl', 'allow_self_signed', true);

            if ($this->local_cert !== null) {
                stream_context_set_option($context, 'ssl', 'local_cert', $this->local_cert);
            }
        } else {
            $proto = 'tls';
        }

        $target = sprintf('%s://%s:%d', $proto, $this->host, $this->port);

        if (isset($context) && is_resource($context)) {
            $this->socket = @stream_socket_client($target, $errno, $errstr, $this->connect_timeout, STREAM_CLIENT_CONNECT, $context);
        } else {
            $this->socket = @stream_socket_client($target, $errno, $errstr, $this->connect_timeout, STREAM_CLIENT_CONNECT);
        }

        if ($this->socket === false) {
            throw new Exception($errstr, $errno);
        }

        // set stream time out
        if (!stream_set_timeout($this->socket, $this->timeout)) {
            throw new Exception('unable to set stream timeout');
        }

        // set to non-blocking
        if (!stream_set_blocking($this->socket, 0)) {
            throw new Exception('unable to set blocking');
        }

        // get greeting
        $greeting = $this->getFrame();

        // login
        try {
            $this->login();
        } catch (Exception $e) {
            return false;
        }

        // return greeting
        return $greeting;
    }


    /**
     * This is overriden because we don't use logout command
     *
     */
    public function close() {
        return true;
    }
}

class Arnes extends EPP
{
    /**
     * Constructs a new ARNES register wrapper and initializes it.
     */
    function __construct( $conf )
    {
        $this->_username = $conf['username'];
        $this->_password = $conf['password'];
        $this->_transport = $conf['transport'];
        $this->_hostname = $conf['host'];
        $this->_port = $conf['port'];
        $this->_idGateway = $conf['idGateway'];

        $this->init();
    }

    /**
     * Creates a new EPP_Client instance and sets its properties.
     * @throws ERegister ERR_CONNECTION_FAILED on login error.
     */
    private function init()
    {
        $this->error = '';

        $this->_epp = new ArnesEPPClient([
            'host' => $this->_hostname,
            'port' => $this->_port,
            'username' => $this->_username,
            'password' => $this->_password,
            'services' => [
                'urn:ietf:params:xml:ns:domain-1.0',
                'urn:ietf:params:xml:ns:host-1.0',
                'urn:ietf:params:xml:ns:contact-1.0'
            ],
            'serviceExtensions' => [
                'http://www.arnes.si/xml/epp/dnssi-1.2',
                'http://www.arnes.si/xml/epp/DNScheck-1.0'
            ],
            'debug' => false,
        ]);

        if ( !$this->_epp->connect() ) {
            //throw new Domovanje_Register_ERegister(Domovanje_Register_ERegister::ERR_CONNECTION_FAILED);
            return false;
        }

        return true;
    }

    public function readMessages()
    {
        $messages = parent::readMessages();
        foreach ($messages as $msg) {
            $newMsg = new \App\Msg();

            if (strpos($msg['title'], 'transfer to registrar ' . strtolower($this->_username) . ' APPROVED') !== false && strtolower($msg['message']['trnData']['reID']) == strtolower($this->_username)) {
                $newMsg->msgAction = self::DOMAIN_TRANSFER_IN;
                $newMsg->domain = $msg['message']['trnData']['name'];
            } else if (strpos($msg['title'], 'transfer to registrar') !== false && strpos($msg['title'], 'APPROVED') !== false && $msg['message']['trnData']['trStatus'] == 'clientApproved') {
                $newMsg->msgAction = self::DOMAIN_TRANSFER_OUT;
                $newMsg->domain = $msg['message']['trnData']['name'];
            } else if ($msg['title'] && strpos($msg['title'], 'has been deleted from .si') !== false) {
                $newMsg->msgAction = self::DOMAIN_DELETED;
                $newMsg->domain = $msg['message']['trnData']['name'];
            }

//            if (!$newMsg->msgAction) {
//                continue;
//            }

            $newMsg->idGateway = $this->_idGateway;
            $newMsg->msgDate = date('Y-m-d H:i:s',strtotime($msg['date']));
            $newMsg->msg = json_encode($msg);
            $newMsg->msgId = $msg['messageID'];
            $newMsg->save();
        }

        return true;
    }

}
