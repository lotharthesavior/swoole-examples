<?php

namespace App\Middlewares;

use Conveyor\ActionMiddlewares\Interfaces\MiddlewareInterface;
use Swoole\Table;
use Exception;

class DeleteMessageMiddleware implements MiddlewareInterface
{
	/**
     * @param mixed $payload
     *
     * @throws Exception
     */
    public function __invoke($payload)
    {
    	$data = $payload->getParsedData();
    	$fd = $payload->getFd();
    	$server = $payload->getServer();
    	$messagesTable = $server->message_table;

    	try {
    		/** @throws Exception */
    		$this->isUserAllowedToDeleteMessage($fd, $data, $messagesTable);
    	} catch (Exception $e) {
    		$server->push($fd, json_encode([
    			'action' => 'error',
    			'message' => $e->getMessage(),
    		]));
    		throw $e;
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
    public function isUserAllowedToDeleteMessage(int $fd, array $data, Table $messagesTable)
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
