<?php

namespace App\Actions;

use Conveyor\Actions\Interfaces\ActionInterface;

class AddMessage implements ActionInterface
{
    /** @var string */
    protected $name = 'add-message';

    /** @var int */
    protected $fd;

    /** @var mixed */
    protected $server;

    public function execute(array $data)
    {
        $user_name = $this->server->user_table->get($this->fd, 'name');

        $next_id = $this->server->message_table->count() + 1;
        $this->server->message_table->set($next_id, [
            'fd' => $this->fd,
            'user_name' => $user_name,
            'message' => $data['message'],
        ]);

        $connections = $this->server->connection_list(0);

        foreach ($connections as $fd) {
            if ($this->fd === $fd) {
                $message = json_encode([
                    'id' => $next_id,
                    'action' => $this->name,
                    'message' => 'My message: ' . $data['message'],
                ]);
            } else {
                $message = json_encode([
                    'id' => $next_id,
                    'action' => $this->name,
                    'message' => $user_name . '\'s message: ' . $data['message'], 
                ]);
            }
            $this->server->push($fd, $message);
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
