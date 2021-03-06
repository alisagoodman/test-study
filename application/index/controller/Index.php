<?php

namespace app\index\controller;

use app\index\model\User;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\exception\PDOException;
use think\session\driver\Redis;

class Index
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    public function send()
    {
        echo phpinfo();die;
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('task_queue', false, false, false, false);
        if (empty($data)) $data = "Hello World!";
        $msg = new AMQPMessage($data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT) # 使消息持久化
        );

        $channel->basic_publish($msg, '', 'task_queue');

        echo " [x] Sent ", $data, "\n";
    }

    public function swoole()
    {
        $http = $this->swoole();
    }

    public function getUserList()
    {       
        $userModel = new User();
        $where = [
            'id' => 1,
        ];
        $list = $userModel->selectOrFail($where);
        return $list;
    }

    public function getRedis()
    {
        $now = time();
        $redis = new \think\cache\driver\Redis();
        $redis->set("test-study" . $now, "123");
        return $redis->get("test-study" . $now);
    }

    public function addUser()
    {
        $userModel = new User();
        $arr = [
            'name' => '小明',
            'age' => 18,
            'create_time' => time(),
        ];
        $res = $userModel->insert($arr);
        return $res;
    }

}
