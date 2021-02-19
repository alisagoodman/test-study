<?php

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class ReceiveLogDirect extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('receive_log_direct')
            ->addArgument('error', Argument::OPTIONAL, "your name")
            ->addArgument('argv', Argument::OPTIONAL, "your name");
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        $severities = [
            0 => 'info',
//            1 => 'error',
//            2 => 'warning'
        ];
        $severity = $input->getArgument('error') ? : 'info';
        $data = $input->getArgument('argv');
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare('direct_logs', 'direct', false, false, false);

        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

//        foreach($severities as $severity) {
//            $channel->queue_bind($queue_name, 'direct_logs', $severity);
//        }
        $channel->queue_bind($queue_name, 'direct_logs', $severity);

        echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

        $callback = function($msg){
            echo ' [x] ',$msg->delivery_info['routing_key'], ':', $msg->body, "\n";
        };

        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

    }
}
