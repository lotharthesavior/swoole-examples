<?php

use Swoole\HTTP\Server;
use Swoole\Http\Response;
use Swoole\Http\Request;

$server = new Server("0.0.0.0", 8080, SWOOLE_PROCESS);

$server->on("start", function($server) {
	$pid_file = __DIR__ . '/http-server-pid';
	if (file_exists($pid_file)) {
		unlink($pid_file);
	}	
	file_put_contents($pid_file, $server->master_pid);
    echo 'Server started with PID: ' . $server->master_pid . " at http://127.0.0.1:8080\n";
});

$server->on('request', function(Request $request, Response $response) {
	$response->header('Content-Type', 'application/json');

	co::sleep(1);
	echo "executing...";

	if (isset($request->get['user'])) {
		$response->end(json_encode([
			1 => [
				"title" => "title 1",
				"content" => "some content",
			],
			2 => [
				"title" => "title 2",
				"content" => "some content 2",
			],
		]));
		return;
	} else {
		$response->end(json_encode([
			1 => [
				"name" => "John Galt",
				"email" => "john@galt.com",
			],
			2 => [
				"name" => "Luke Skywalker",
				"email" => "luke@skywalker.com",
			],
			3 => [
				"name" => "Luke Skywalker 2",
				"email" => "luke@skywalker2.com",
			],
		]));
		return;
	}

	$response->end(json_encode([
		'error' => 1,
		'message' => 'It shouldn\'t be here',
	]));
});

$server->start();
