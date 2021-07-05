<?php

use App\Actions\AddMessage;
use App\Actions\DeleteMessage;
use Conveyor\SocketHandlers\Interfaces\SocketHandlerInterface;
use Conveyor\SocketHandlers\SocketMessageRouter;

return function (): SocketHandlerInterface {
    $addMessage = new AddMessage();
    $deleteMessage = new DeleteMessage();

    $socketRouter = new SocketMessageRouter();
    $socketRouter->add($addMessage);
    $socketRouter->add($deleteMessage);

    return $socketRouter;
};
