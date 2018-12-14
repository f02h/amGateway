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

    public function retrieve($procID, $ignoreTimeout=false) {

        if (!$procID) {
            throw new Domovanje_Register_ERegister(Domovanje_Register_ERegister::ERR_UNKNOWN, 'Action does not offer proc-id or tracking-id.');
        }

        if ($ignoreTimeout) {
            $numRetries = 1;
        } else {
            $numRetries = Domovanje_Register_Joker::TIMEOUT;
        }

        for ($i=0; $i<$numRetries; $i++) {
            $result = $this->retrieveResult($procID);

            if ($result['response_header']['status-code']==0) {
                $ackTmp = substr($result['response_body'], strpos($result['response_body'], 'Completion-Status: ')+19, 3); // returns 'ack', 'nack' or '?' for busy

                if ($ackTmp == 'ack') {
                    // success
                    break;
                } else
                    if ($ackTmp == 'nac') {
                        // failed
                        if (strpos($this->getResponse(), 'Permission denied:')!==false) {
                            throw new Domovanje_Register_ERegister(Domovanje_Register_ERegister::ERR_DOMAIN_OPERATION_NOT_ALLOWED, 'Operation not allowed. Customer has no rights for this domain!', $result['response_header']['status-code']);
                        } else
                            if (strpos($this->getResponse(), 'Nameserver not found in the registry')!==false) {
                                preg_match("/host=(.*)&/i", $params, $dns);
                                throw new Domovanje_Register_ERegister(Domovanje_Register_ERegister::ERR_DNS_UNKNOWN, 'DNS \'' . $dns[1] . '\' does not exist in the register.', $result['response_header']['status-code']);
                            } else if(strpos($this->getResponse(), 'The domain name already has an authorisation code.') !== false) {
                                // skip message if error setting transfer code
                                break;
                            } else {
                                throw new Domovanje_Register_ERegister(Domovanje_Register_ERegister::ERR_UNKNOWN, isset($result['response_header']['status-text'])?$result['response_header']['status-text']:'Request failed. Unknown error.', $result['response_header']['status-code']);
                            }
                    } else
                        if ($ackTmp == '?') {
                            // timeout, retry
                            $result = '';
                        } else
                            if ($result['response_header']['status-text']!='Request has been written to mailbox') {
                                // timeout, retry
                                $result = '';
                            } else
                                if (!$ackTmp) {
                                    // command successfully executed, but not the ACK/NACK type of message
                                    break;
                                }
            } else {
                throw new Domovanje_Register_ERegister(Domovanje_Register_ERegister::ERR_UNKNOWN, $result['response_header']['error'], $result['response_header']['status-code']);
            }

            sleep(2);
        }

        if (!$result) {
            throw new Domovanje_Register_ERegister(Domovanje_Register_ERegister::ERR_TIMEOUT, 'Joker timeout.' );
        }

        return $result;
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
