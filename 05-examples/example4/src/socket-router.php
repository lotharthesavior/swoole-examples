<?php

use App\Actions\AddMessage;
use App\Actions\DeleteMessage;
use App\Middlewares\OriginMiddleware;
use App\Middlewares\DeleteMessageMiddleware;
use Conveyor\SocketHandlers\Interfaces\SocketHandlerInterface;
use Conveyor\SocketHandlers\SocketMessageRouter;

return function (): SocketHandlerInterface {
    $addMessage = new AddMessage();
    $deleteMessage = new DeleteMessage();

    $socketRouter = new SocketMessageRouter();
    $socketRouter->add($addMessage);
    $socketRouter->add($deleteMessage);

    $originMiddleware = new OriginMiddleware();
    $deleteMessageMiddleware = new DeleteMessageMiddleware();

    // Delete actions middlewares.
    $socketRouter->middleware($deleteMessage->getName(), $originMiddleware);
    $socketRouter->middleware($deleteMessage->getName(), $deleteMessageMiddleware);

    // Add actions middlewares.
    $socketRouter->middleware($addMessage->getName(), $originMiddleware);

    return $socketRouter;
};
