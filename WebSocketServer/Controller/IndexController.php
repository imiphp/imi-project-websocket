<?php

namespace ImiApp\WebSocketServer\Controller;

use Imi\App;
use Imi\Server\WebSocket\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;

/**
 * 数据收发测试.
 * @WSController
 */
class IndexController extends WebSocketController
{
    /**
     * 发送消息.
     *
     * @WSAction
     * @WSRoute({"action"="send"})
     * @param
     * @return array
     */
    public function send($data)
    {
        $address = $this->frame->getClientAddress();
        $message = '['.$address->getAddress().':'.$address->getPort().']: '.$data->message;

        return [
            'success' => true,
            'data'    => $message,
            'mode'    => App::getApp()->getType(),
        ];
    }
}
