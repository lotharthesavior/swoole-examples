<?php

use Swoole\HTTP\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

if (!defined('PID_FILE')) {
    define('PID_FILE', './http-server-pid');
}

if (file_exists(PID_FILE)) {
    echo "Server already running at the pid: " . file_get_contents(PID_FILE) . "\n";
    exit;
}

$server = new Server("0.0.0.0", 9503, SWOOLE_PROCESS);

$server->on("start", function (Server $server) {
    file_put_contents(PID_FILE, $server->master_pid);
    echo "HTTP server available at http://127.0.0.1:9503 (PID " . $server->master_pid . ")\n";
});

$server->on("request", function (Request $request, Response $response) {
    echo "Incoming connection time: " . date('Y-m-d H:i:s') . "\n";
    echo "Incoming connection uri: " . $request->server['request_uri'] . "\n";
    $response->header("Content-Type", "text/plain");
    $response->end("Server response content.");
});

$server->on('close', function ($server) {
    echo "Connection closed.\n";
});

$server->start();
