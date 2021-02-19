<?php

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class ReceiveLog extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('receive_log');
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare('logs', 'fanout', false, false, false);

        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

        $channel->queue_bind($queue_name, 'logs');

        echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

        $callback = function($msg){
            echo ' [x] ', $msg->body, "\n";
        };

        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
