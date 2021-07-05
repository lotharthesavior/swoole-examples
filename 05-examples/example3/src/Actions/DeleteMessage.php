<?php

namespace App\Actions;

use Conveyor\Actions\Interfaces\ActionInterface;

class DeleteMessage implements ActionInterface
{
    /** @var string */
    protected $name = 'delete-message';

    /** @var int */
    protected $fd;

    /** @var mixed */
    protected $server;

    public function execute(array $data)
    {
        $user_name = $this->server->user_table->get($this->fd, 'name');

        $this->server->message_table->del($data['message_id']);

        $connections = $this->server->connection_list(0);

        foreach ($connections as $fd) {
            $this->server->push($fd, json_encode([
                'action' => $this->name,
                'delete_message_id' => $data['message_id'],
            ]));
        }
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setFd(int $fd): void
    {
        $this->fd = $fd;
    }

    public function setServer($server): void
    {
        $this->server = $server;
    }
}
