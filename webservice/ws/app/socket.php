<?php

namespace WS;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use DatabaseChat\DatabaseChat;
use GuzzleHttp\Client;



class Socket implements MessageComponentInterface {

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->sessions = array();        
    }

    public function onOpen(ConnectionInterface $conn) {
        print_r("Abriu \n");
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $json = json_decode($msg);

        if(isset($json->_registroSessao)){
            $this->verficarSessao($json->_registroSessao,$from->resourceId);
        } elseif (isset($json->user)) {

            $response = $this->WebService("POST","newchat=true&chat=".$msg);
            $json = json_decode( $response );
            
            $user = $json->user->_id;
            $ticket = $json->ticket;

            foreach ( $this->clients as $client ) {
                if ( isset($this->sessions[$user]) && $this->sessions[$user] == $client->resourceId ||  isset($this->sessions[$ticket]) && $this->sessions[$ticket] == $client->resourceId) {
                    $client->send($response);
                }
                
            }
        }   
    }

    public function onClose(ConnectionInterface $conn) {
        print_r("Saiu \n");

    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }

    public function verficarSessao($id,$sessao){
        if(!isset($this->sessions[$id])){
            $this->sessions[$id] = $sessao;
        }else{
            $this->sessions[$id] = $sessao;
        }
        return true;
    }
    public function WebService($request,$headers)
    {
        $client  =  new Client();
        $response = $client->request($request,'http://localhost/webservice/v1/chat/?'.$headers);
        return $response->getBody()->getContents();

    }
}
?>