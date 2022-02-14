<?php

namespace App\Actions;

use Conveyor\Actions\Abstractions\AbstractAction;
use Exception;

class DeleteMessage extends AbstractAction
{
    protected string $name = 'delete-message';

    public function execute(array $data): mixed
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

    public function validateData(array $data): void
    {
        // TODO: Implement validateData() method.
    }
}
