<?php

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class NewTask extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('new_task')
            ->addArgument('argv', Argument::OPTIONAL, "your name")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name');
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        var_dump('11111');die;
        $data = $input->getArgument('argv');
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('task_queue', false, false, false, false);
        if(empty($data)) $data = "Hello World!";
        $msg = new AMQPMessage($data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT) # 使消息持久化
        );

        $channel->basic_publish($msg, '', 'task_queue');

        echo " [x] Sent ", $data, "\n";
    }
}
