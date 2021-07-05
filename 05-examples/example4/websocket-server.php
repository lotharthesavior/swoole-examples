<?php

use Conveyor\SocketHandlers\Interfaces\SocketHandlerInterface;
use Swoole\Websocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

require __DIR__ . '/vendor/autoload.php';

/** @var SocketHandlerInterface $socket_router */
$socket_router = (require_once __DIR__ . '/src/socket-router.php')();

$server = new Server("0.0.0.0", 9501);
$server->user_table = (require __DIR__ . DIRECTORY_SEPARATOR . 'user-table.php')();
$server->message_table = (require __DIR__ . DIRECTORY_SEPARATOR . 'message-table.php')();

$server->on("start", function (Server $server) {
    echo 'Swoole WebSocket Server is started at http://127.0.0.1:9501' . PHP_EOL;
});

$server->on('open', function(Server $server, Request $request) {
    if (!isset($request->server['query_string'])) {
        $server->disconnect($request->fd, 401, 'Please, inform your name for this connection.');
        return;
    }

    $acceptable_origins = ['http://127.0.0.0:8080'];

    if (!in_array($request->header['origin'], $acceptable_origins)) {
        $server->disconnect($request->fd, 401, 'Not authorized procedure!');
        return;
    }

    parse_str($request->server['query_string'], $parsed_query);
    $server->user_table->set($request->fd, [
        'id' => $request->fd,
        'name' => $parsed_query['name'],
    ]);

    echo 'Connection open: ' . $request->fd . PHP_EOL;
});

$server->on('message', function(Server $server, Frame $frame) use ($socket_router) {
    $user_name = $server->user_table->get($frame->fd, 'name');

    // var_dump($frame->fd);
    var_dump($server->getClientInfo ($frame->fd));
    // var_dump($server->connection_info($frame->fd));
    // var_dump($server->user_table->get($frame->fd));

    echo 'Received message (' . $user_name . '): ' . $frame->data . PHP_EOL;

    $socket_router->handle($frame->data, $frame->fd, $server);
});

$server->on('close', function(Server $server, $fd) {
    echo 'Connection close: ' . $fd . PHP_EOL;
    $server->user_table->del($fd);
});

$server->start();
