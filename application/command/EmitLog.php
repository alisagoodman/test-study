<?php

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class EmitLog extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('emit_log')
            ->addArgument('argv', Argument::OPTIONAL, "your name");
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        $data = $input->getArgument('argv');
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare('logs', 'fanout', false, false, false);
        if(empty($data)) $data = "info: Hello World!";
        $msg = new AMQPMessage($data);

        $channel->basic_publish($msg, 'logs');

        echo " [x] Sent ", $data, "\n";

        $channel->close();
        $connection->close();
    }
}
