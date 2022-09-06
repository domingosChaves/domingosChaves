<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use WS\Socket;

require dirname( __FILE__ ) . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Socket()
        )
    ),
    9090
);

$server->run();

?>