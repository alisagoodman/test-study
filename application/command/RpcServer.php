<?php

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class RpcServer extends Command
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
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('rpc_queue', false, false, false, false);

        function fib($n) {
            if ($n == 0)
                return 0;
            if ($n == 1)
                return 1;
            return fib($n-1) + fib($n-2);
        }

        echo " [x] Awaiting RPC requests\n";
        $callback = function($req) {
            $n = intval($req->body);
            echo " [.] fib(", $n, ")\n";

            $msg = new AMQPMessage(
                (string) fib($n),
                array('correlation_id' => $req->get('correlation_id'))
            );

            $req->delivery_info['channel']->basic_publish(
                $msg, '', $req->get('reply_to'));
            $req->delivery_info['channel']->basic_ack(
                $req->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
