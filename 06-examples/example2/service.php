<?php

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

$chan = new Channel(1);

function get_users(): array {
	$raw_data = file_get_contents(__DIR__ . '/data.json');
	return json_decode($raw_data, true);
}

Co\run(function() use ($chan) {

	// Coroutine producing data.
	go(function() use ($chan) {
		$cid = Coroutine::getuid();
		$i = 0;
		while (1) {
			$users = get_users(); // ~1 sec
			$chan->push(['users' => $users]);
			$i++;
		}
	});

	// Coroutine printing data.
	go(function() use ($chan) {
		while(1) {
			system('clear');

			$data = $chan->pop();
			foreach ($data['users'] as $user) {
				echo 'User:' . $user['name'] . "\n";
			}

			co::sleep(1.0);
		}
	});

});
