<?php

namespace app\index\controller;

//require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
class Train
{

    public function train()
    {
        echo 'qqq';
    }

    public function connect()
    {
        $conf = [
            'host' => '127.0.0.1',
            'port' => 5672,
            'user' => 'guest',
            'pwd' => 'guest',
            'vhost' => '/',
        ];
        $exchangeName = 'kd_sms_send_ex'; //交换机名
        $queueName = 'kd_sms_send_q'; //队列名称
        $routingKey = 'sms_send'; //路由关键字(也可以省略)

        $conn = new AMQPStreamConnection( //建立生产者与mq之间的连接
            $conf['host'], $conf['port'], $conf['user'], $conf['pwd'], $conf['vhost']
        );
        $channel = $conn->channel(); //在已连接基础上建立生产者与mq之间的通道


        $channel->exchange_declare($exchangeName, 'direct', false, true, false); //声明初始化交换机
        $channel->queue_declare($queueName, false, true, false, false); //声明初始化一条队列
        $channel->queue_bind($queueName, $exchangeName, $routingKey); //将队列与某个交换机进行绑定，并使用路由关键字

        $msgBody = json_encode(["name" => "iGoo", "age" => 22]);
        $msg = new AMQPMessage($msgBody, ['content_type' => 'text/plain', 'delivery_mode' => 2]); //生成消息
        $r = $channel->basic_publish($msg, $exchangeName, $routingKey); //推送消息到某个交换机
        $channel->close();
        $conn->close();

    }
}
