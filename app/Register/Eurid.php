<?php

namespace App\Register;

use App\Msg;
use Exception;

use AfriCC\EPP\Client as EPPClient;

use AfriCC;

class EuridEPPClient extends EPPClient
{
    /**
     * This is overriden because why?
     *
     * @return null
     * @throws Exception
     */
    public function connect()
    {
        if ($this->ssl && 0) {
            $proto = 'ssl';

            $context = stream_context_create();
            stream_context_set_option($context, 'ssl', 'verify_peer', false);
            stream_context_set_option($context, 'ssl', 'allow_self_signed', true);

            if ($this->local_cert !== null) {
                stream_context_set_option($context, 'ssl', 'local_cert', $this->local_cert);
            }
        } else {
            $proto = 'tls';
            $context = stream_context_create();
            stream_context_set_option($context, 'ssl', 'verify_peer', false);
            stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
            stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
        }
        $target = sprintf('%s://%s:%d', $proto, $this->host, $this->port);
        if (isset($context) && is_resource($context)) {
            $this->socket = stream_socket_client($target, $errno, $errstr, $this->connect_timeout, STREAM_CLIENT_CONNECT, $context);
        } else {
            $this->socket = stream_socket_client($target, $errno, $errstr, $this->connect_timeout, STREAM_CLIENT_CONNECT);
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
        $this->login();

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

class Eurid extends EPP
{
    const NAMESPACE_URI = 'http://www.eurid.eu/xml/epp/';

    /**
     * Constructs a new ARNES register wrapper and initializes it.
     */
    function __construct( $conf )
    {
        $this->_username = $conf['username'];
        $this->_password = $conf['password'];
        $this->_transport = $conf['transport'];
        $this->_hostname = $conf['hostname'];
        $this->_port = $conf['port'];

        $this->init();
    }

    /**
     * Creates a new EPP_Client instance and sets its properties.
     * @throws ERegister ERR_CONNECTION_FAILED on login error.
     */
    private function init()
    {
        $this->error = '';

        $this->_epp = new EuridEPPClient([
            'host' => $this->_hostname,
            'port' => $this->_port,
            'username' => $this->_username,
            'password' => $this->_password,
            'timeout' => 20,
            'connect_timeout' => 50,
            'services' => [
                'urn:ietf:params:xml:ns:domain-1.0',
                'urn:ietf:params:xml:ns:contact-1.0',
            ],
            'serviceExtensions' => [
                'http://www.eurid.eu/xml/epp/contact-ext-1.1',
                'http://www.eurid.eu/xml/epp/authInfo-1.1',
                'http://www.eurid.eu/xml/epp/domain-ext-2.1',
                'http://www.eurid.eu/xml/epp/poll-1.2'
            ]
        ]);

        try {
            $this->_epp->connect();
        } catch (Exception $e) {
            print $e;
            return false;
        }

        return true;
    }

    public function readMessages()
    {
        $frame = new AfriCC\EPP\Frame\Command\Poll;
        $frame->request();

        $eppElm = $frame->getElementsByTagName('epp')[0];
        $eppElm->setAttribute('xmlns:poll', 'http://www.eurid.eu/xml/epp/poll-1.2');

        $this->_response = $this->_epp->request($frame);

        if (!($this->_response instanceof AfriCC\EPP\Frame\Response)) {
            return;
        }

        $messages = array();
        if(is_a($this->_response, 'AfriCC\EPP\Frame\Response\MessageQueue')) {
            $i = $this->_response->queueCount();
            //while($this->_response->queueCount()) {
            while($i > 0) {
                $messages[] = array(
                    'date' => $this->_response->queueDate(),
                    'title' => $this->_response->queueMessage(),
                    'messageID' => $this->_response->queueId(),
                    'message' => $this->_response->data() ? $this->_response->data() : '',
                    'response' => $this->_response
                );

                //$frame = new AfriCC\EPP\Frame\Command\Poll;
                //$frame->ack($this->_response->queueId());
                //$this->_epp->request($frame);
                $i--;

                $frame = new AfriCC\EPP\Frame\Command\Poll;
                $frame->request();
                $this->_response = $this->_epp->request($frame);

                if(!is_a($this->_response, 'AfriCC\EPP\Frame\Response\MessageQueue')) {
                    break;
                }
            }
        }

        foreach ($messages as $msg) {
            if (!Msg::where('msgId', $msg['messageID'])->first()) {

                $pollData = $msg['message']['pollData'];

                $newMsg = new \App\Msg();
                $newMsg->idGateway = 'Eurid';
                $newMsg->domain = $pollData['context'] == 'TRANSFER' && $pollData['objectType'] == 'DOMAIN' ? $pollData['object'] : '';
                $newMsg->msgAction = $pollData['context'] == 'TRANSFER' && $pollData['action'] == 'AWAY' ? 'TRANSFER_OUT' : '';
                $newMsg->msgDate = $msg['date'];
                $newMsg->msg = $msg['response'];
                $newMsg->msgId = $msg['messageID'];
                $newMsg->save();
            }
        }

        return true;

        return $messages;
    }
}
