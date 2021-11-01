<?php

use Swoole\Process;
use Swoole\Timer;
use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Coroutine\System;
use Swoole\WebSocket\Frame;
use Swoole\Table;

define('SERVER_DEAD', 'dead');
define('SERVER_ALIVE', 'alive');

// table for actions
$actions_table = new Table(1024);
$actions_table->column('data', Table::TYPE_STRING, 64);
$actions_table->create();
$http_status_table_key = 'http_status'; // key for http status
$ws_table_key = 'ws_input'; // key for ws communication

/**
 * @return int|null
 */
function get_http_server_pid() {
    $http_process_name = 'swoole-http-server';
    // list processes by name (first)
    $pid = System::exec('/usr/bin/ps -aux | grep ' . $http_process_name . ' | grep -v \'grep swoole-http-server\' | /usr/bin/awk \'{ print $2; }\' | /usr/bin/sed -n \'1,1p\'');
    $clean_pid = trim($pid['output']);
    return (int) $clean_pid ? (int) $clean_pid : null;
}

// web socket server where we can see and control the status
$ws_process = new Process(function(Process $worker) use (
    $actions_table, $ws_table_key, $http_status_table_key
) {
    $timers = [];

    $worker->name('swoole-websocket-server');

    $ws_server = new Server('0.0.0.0', 8080, SWOOLE_BASE);

    $ws_server->set([
        'document_root' => getcwd(),
        'enable_static_handler' => true,
    ]);

    $ws_server->on('open', function(Server $ws_server, Request $request) use (
        $worker, $actions_table, $http_status_table_key, &$timers
    ) {
        echo 'Connection open: ' . $request->fd . PHP_EOL;

        $timers[$request->fd] = Timer::tick(1000, function() use (
            $ws_server, $request, $actions_table, $http_status_table_key
        ) {
            $status = $actions_table->get($http_status_table_key);
            $actions_table->del($http_status_table_key);
            
            if (!isset($status['data'])) {
                return;
            }

            if($ws_server->isEstablished($request->fd)) {
                $ws_server->push($request->fd, json_encode([
                    'id' => $request->fd,
                    'data' => $status['data'],
                ]));
            }
        });
    });

    $ws_server->on('request', function(Request $request, Response $response) {
        $response->redirect('/index.html');
    });

    $ws_server->on('message', function(Server $ws_server, Frame $frame) use (
        $actions_table, $ws_table_key
    ) {
        echo 'Received message: ' . $frame->data . PHP_EOL;
        $actions_table->set($ws_table_key, [
            'data' => $frame->data,
        ]);
    });

    $ws_server->on('close', function(Server $ws_server, $fd) use (&$timers) {
        echo 'Connection close: ' . $fd . PHP_EOL;
        Timer::clear($timers[$fd]);
        unset($timers[$fd]);
    });

    $ws_server->start();
});
$ws_process->start();

// start http server
$http_process = new Process(function(Process $worker) {
    Co\run(function() {
        System::exec('/usr/bin/php $(pwd)/server.php');
    });
});
$http_process->start();

// here we monitor the server and restart it if it is not online
$monitor_process = new Process(function(Process $worker) use (
    $actions_table, $http_status_table_key
) {
    Timer::tick(1000, function() use (
        $actions_table, $http_status_table_key
    ) {
        $clean_pid = get_http_server_pid();
        $status = $clean_pid === null ? SERVER_DEAD : SERVER_ALIVE;
        $actions_table->set($http_status_table_key, [
            'data' => $status,
        ]);
    });
});
$monitor_process->start();

// communication websocket => actions
$actions_communication = new Process(function() use (
    $ws_process, $actions_table, $ws_table_key
) {
    Timer::tick(1000, function() use ($actions_table, $ws_table_key) {
        $action = $actions_table->get($ws_table_key);
        $actions_table->del($ws_table_key);

        if (!isset($action['data'])) {
            // nothing here.
        } elseif ($action['data'] === 'start') {
            echo 'Starting...' . PHP_EOL;
            $clean_pid = get_http_server_pid();
            if ($clean_pid === null) {
                System::exec('/usr/bin/php $(pwd)/server.php');
            }
        } elseif ($action['data'] === 'stop') {
            echo 'Stopping...' . PHP_EOL; 
            $clean_pid = get_http_server_pid();
            if ($clean_pid !== null) {
                Process::kill($clean_pid, SIGKILL);
            }
        }
    });
});
$actions_communication->start();

// listening for the kill signals
Co\run(function() {
    $info = System::waitSignal(SIGKILL, -1);
});
