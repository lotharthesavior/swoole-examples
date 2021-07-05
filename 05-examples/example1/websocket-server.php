<?php

use Swoole\Websocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

$server = new Server("0.0.0.0", 9501);

$server->on('start', function (Server $server) {
    echo 'Swoole WebSocket Server is started at http://127.0.0.1:9501' . PHP_EOL;
});

$server->on('open', function(Server $server, Request $request) {
    echo "Connection open: {$request->fd}\n";
});

$server->on('message', function(Server $server, Frame $frame) {
    echo 'Received message: ' . $frame->data . PHP_EOL;
    $server->push($frame->fd, 'Your message: ' . $frame->data);
});

$server->on('close', function(Server $server, $fd) {
    echo 'connection close: ' . $fd . PHP_EOL;
});

$server->start();
