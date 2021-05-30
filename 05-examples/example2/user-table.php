<?php

use Swoole\Table;

return function(): Table {
    $table = new Table(1024, 1);
    $table->column('id', Swoole\Table::TYPE_INT);
    $table->column('name', Swoole\Table::TYPE_STRING, 64);
    $table->create();
    return $table;
};
