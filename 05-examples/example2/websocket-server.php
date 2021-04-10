<?php

use Swoole\Websocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

$table = (require __DIR__ . DIRECTORY_SEPARATOR . 'table.php')();

$server = new Server("0.0.0.0", 9501);
$server->table = $table;

$server->on("start", function (Server $server) {
    echo "Swoole WebSocket Server is started at http://127.0.0.1:9501\n";
});

$server->on('open', function(Server $server, Request $request) {
    if (!isset($request->server['query_string'])) {
        $server->disconnect($request->fd, 401, 'Please, inform your name for this connection.');
        return;
    }

    parse_str($request->server['query_string'], $parsed_query);
    var_dump($parsed_query);
    $server->table->set($request->fd, ['id' => $request->fd, 'name' => $parsed_query['name']]);

    echo "Connection open: {$request->fd}\n";
});

$server->on('message', function(Server $server, Frame $frame) {
    $user_name = $server->table->get($frame->fd, 'name');

    echo 'Received message: ' . $frame->data . PHP_EOL;
    echo 'Received message from: ' . $user_name . PHP_EOL;

    $connections = $server->connection_list(0);
    foreach ($connections as $fd) {
        if ($frame->fd === $fd) {
            $server->push($fd, 'Your message: ' . $frame->data);
        } else {
            $server->push($fd, $user_name . '\'s message: ' . $frame->data);
        }
    }
});

$server->on('close', function(Server $server, $fd) {
    echo "connection close: {$fd}\n";
});

$server->start();
