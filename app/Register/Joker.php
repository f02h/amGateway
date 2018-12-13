<?php

namespace App\Register;

class Joker extends EPP
{

    private $_authID;
    /**
     * Constructs a new Joker register wrapper and initializes it.
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
        // First obtain an Auth-ID
        $fields = 'username='.urlencode( $this->_username ).'&password='.urlencode( $this->_password );
        $result = $this->sendRequest('login', $fields);
        $result = $this->parseResponse($result);
        $this->_authID = $result['response_header']['auth-sid'];

        if (!$this->_authID) {
            return false;
        }

        return true;
    }

    public function readMessages()
    {

        $result = $this->sendRequest('result-list');
        $result = $this->parseResponse($result);

        $messages = array();

        if ( $result['response_body'] ) {
            $messagesText = explode("\n", $result['response_body'] );
            foreach ($messagesText as $msg) {
                $msgPart = explode(" ", $msg );
                $msgArr = array(
                    'date' => $this->convertJokerDate($msgPart[0]),
                    'messageID' => $msgPart[1],
                    'title' => $msgPart[3].' '.$msgPart[4].' '.$msgPart[5],
                    'message' => array('trnData' => $msg)
                );

                if (strpos($msgArr['title'], 'domain-transfer-get-auth-id') !== false) {
                    $result = $this->retrieve($msgPart[2]);
                    $msgArr['domainPassword'] = $result['domainPassword'];
                }
                $messages[] = $msgArr;

                if ($msgPart[5]=='ack' || $msgPart[5]=='nack') {
                    $this->sendRequest('result-delete', 'SvTrID='.urlencode($msgPart[1]));
                }
            }
        }

        $messages = array_reverse($messages); // order messages by date increasing

        foreach ($messages as $msg) {
            $newMsg = new \App\Msg();

            if (strpos($msg['title'], 'domain-transfer-in') === 0) {
                $newMsg->msgAction = self::DOMAIN_TRANSFER_IN;
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
