<?php

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class EmitLogDirect extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('emit_log_direct')
            ->addArgument('error', Argument::OPTIONAL, "your name")
            ->addArgument('argv', Argument::OPTIONAL, "your name")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name');;
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        $severity = $input->getArgument('error') ? : 'info';
        $data = $input->getArgument('argv');
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare('direct_logs', 'direct', false, false, false);
        if(empty($data)) $data = "Hello World!";

        $msg = new AMQPMessage($data);

        $channel->basic_publish($msg, 'direct_logs', $severity);

        echo " [x] Sent ",$severity,':',$data," \n";

        $channel->close();
        $connection->close();
    }
}
