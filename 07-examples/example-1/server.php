#!/usr/bin/php

<?php

use Swoole\Process;
use Swoole\HTTP\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Coroutine\System;

swoole_set_process_name('swoole-http-server');

$port = 8181;

$server = new Server('0.0.0.0', $port, SWOOLE_BASE);
$server->set([
	'worker_num' => 1,
]);
$server->on('start', function (Server $server) use ($port) {
    echo 'Swoole http server is started at http://127.0.0.1:' . $port . PHP_EOL;
    // echo $server->getMasterPid() . PHP_EOL;
});
$server->on('shutdown', function($server) {
	echo 'Swoole http server is shutting down.' . PHP_EOL;
});
$server->on('request', function (Request $request, Response $response) {
    $response->header('Content-Type', 'text/plain');
    $response->end('Hello World' . PHP_EOL);
});
$server->start();

// Listening for the kill signal.
Co\run(function() {
	$info = System::waitSignal(SIGKILL, -1);
});
