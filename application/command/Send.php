<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Send extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('send');
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);

        $msg = new AMQPMessage('Hello World!');
        $channel->basic_publish($msg, '', 'hello');

        echo " [x] Sent 'Hello World!'\n";
        $channel->close();
        $connection->close();
    }
}
