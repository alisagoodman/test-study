<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use PhpAmqpLib\Connection\AMQPStreamConnection;


class Receive extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('receive');
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };

        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
    }
}
