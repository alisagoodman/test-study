<?php

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Work extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('work');
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('task_queue', false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg){
            echo " [x] Received ", $msg->body, "\n";
            sleep(substr_count($msg->body, '.'));
            echo " [x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        //翻译时注：只有consumer已经处理并确认了上一条message时queue才分派新的message给它
        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('task_queue', '', false, false, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
    }
}
