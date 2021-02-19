<?php

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class EmitLogTopic extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('emit_log_topic')
            ->addArgument('route', Argument::OPTIONAL, "your name")
            ->addArgument('msg', Argument::OPTIONAL, "your name");
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        $routing_key = $input->getArgument('route');
        $data = $input->getArgument('msg');
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare('topic_logs', 'topic', false, false, false);

        $msg = new AMQPMessage($data);

        $channel->basic_publish($msg, 'topic_logs', $routing_key);

        echo " [x] Sent ",$routing_key,':',$data," \n";

        $channel->close();
        $connection->close();
    }
}
