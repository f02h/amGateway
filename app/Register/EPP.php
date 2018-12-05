<?php

namespace App\Register;

use AfriCC;

class EPP
{
    protected $_epp;

    public $_username;
    protected $_password;
    protected $_transport;
    protected $_hostname;
    protected $_port;
    protected $_request;
    protected $_response;
    public $_idGateway;
    public $supportedMessages = array('TRANSFER_OUT' => 'TRANSFER_OUT', 'TRANSFER_IN' => 'TRANSFER_IN');
    public const DOMAIN_TRANSFER_OUT = 'TRANSFER_OUT';
    public const DOMAIN_TRANSFER_IN = 'TRANSFER_IN';

    // Server response codes
    const CMD_SUCCESS = 1000;         // Command completed succesfully
    const CMD_SUCCESS_PENDING = 1001; // Command completed succesfully; Action pending

    /**
     * Constructs a new EPP register wrapper and initializes it.
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
        return true;
    }

    public function disconnect()
    {
        $this->_epp->close();
        return true;
    }

    public function isSynchronous()
    {
        return true;
    }

    public function supportsContactIDs()
    {
        return true;
    }

    public function isDomainTransferPasswordSettable()
    {
        return true;
    }

    public function supportsMessages()
    {
        return true;
    }

    public function getRequest()
    {
        if ($this->_request) {
            return print_r((string)$this->_request, true);
            //return $this->_request->createXML();
        }

        return null;
    }

    public function getResponse()
    {
        return $this->_response->textContent;
        //return print_r($this->_response, true);
    }

    public function readMessages()
    {
        $frame = new AfriCC\EPP\Frame\Command\Poll;
        $frame->request();
        $this->_response = $this->_epp->request($frame);

        if (!($this->_response instanceof AfriCC\EPP\Frame\Response)) {
            throw new Domovanje_Register_ERegister( Domovanje_Register_ERegister::ERR_UNKNOWN, 'Response error', $this->_response->result->code );
            return;
        }

        $messages = array();
        if(is_a($this->_response, 'AfriCC\EPP\Frame\Response\MessageQueue')) {
            while($this->_response->queueCount()) {
                $messages[] = array(
                    'date' => $this->_response->queueDate(),
                    'title' => $this->_response->queueMessage(),
                    'messageID' => $this->_response->queueId(),
                    'message' => $this->_response->data() ? $this->_response->data() : ''
                );

                $frame = new AfriCC\EPP\Frame\Command\Poll;
                $frame->ack($this->_response->queueId());
                $this->_epp->request($frame);

                $frame = new AfriCC\EPP\Frame\Command\Poll;
                $frame->request();
                $this->_response = $this->_epp->request($frame);

                if(!is_a($this->_response, 'AfriCC\EPP\Frame\Response\MessageQueue')) {
                    break;
                }
            }
        }

        return $messages;
    }
}
