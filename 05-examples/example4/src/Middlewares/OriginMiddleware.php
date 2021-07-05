<?php

namespace App\Middlewares;

use Conveyor\ActionMiddlewares\Interfaces\MiddlewareInterface;
use Swoole\Table;
use Exception;

class OriginMiddleware implements MiddlewareInterface
{
	/**
     * @param mixed $payload
     *
     * @throws Exception
     */
    public function __invoke($payload)
    {
    	// var_dump($payload->getServer()->getHeaders());
    	// $data = $payload->getParsedData();
    	// $fd = $payload->getFD();
    	// $server = $payload->getServer();
    	// $messagesTable = $server->message_table;

    	try {
    		/** @throws Exception */
    		// $this->verifyOrigin($fd, $data, $messagesTable);
    	} catch (Exception $e) {
    		// $server->push($fd, json_encode([
    		// 	'action' => 'error',
    		// 	'message' => $e->getMessage(),
    		// ]));
    		// throw $e;
    	}

    	return $payload;
    }

    /**
     * @param int $fd
     * @param array $data
     * @param Table $messagesTable
     * 
     * @throws Exception
     */
    public function verifyOrigin(int $fd, array $data, Table $messagesTable)
    {
    	$message = $messagesTable->get($data['message_id']);

    	if (!$message) {
    		throw new Exception('Message not found in registers.');
    	}

    	if ($message['fd'] !== $fd) {
    		throw new Exception('User not authorized to execute this procedure!');
    	}
    }
}
