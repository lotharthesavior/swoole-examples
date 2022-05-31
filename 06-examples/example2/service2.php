<?php

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

// Let's ignore warnings to keep the screen clean.
error_reporting(E_ERROR | E_PARSE);

$chan = new Channel(1);

function check_site(): bool {
	ini_set('default_socket_timeout', '05');
	set_time_limit(5);
	$f = fopen('http://localhost:8080', 'r');
	
	if ($f) {
		$r = fread($f,1000);
		fclose($f);
	}

	if ( strlen($r) > 1 ) {
		return true;
	}

	return false;
}

Co\run(function() use ($chan) {

	// Coroutine producing data.
	go(function() use ($chan) {
		$cid = Coroutine::getuid();
		$i = 0;
		while (1) {
			$chan->push(['is_online' => check_site()]);
			$i++;
			co::sleep(1.0);
		}
	});

	// Coroutine printing data.
	go(function() use ($chan) {
		$status = null;
		echo 'Starting...' . PHP_EOL;
		while(1) {
			$data = $chan->pop();
			$new_status = $data['is_online'];

			if ($status !== $new_status) {
				system('clear');
				echo 'Status: ' . ($new_status ? 'Online' : 'Offline') . PHP_EOL;
				// here we can send a message via bot, text, email...
				$status = $new_status;
			}
			
			co::sleep(1.0);
		}
	});

});
