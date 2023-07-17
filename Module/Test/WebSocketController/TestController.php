<?php

declare(strict_types=1);

namespace ImiApp\Module\Test\WebSocketController;

use Imi\App;
use Imi\Server\WebSocket\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;

/**
 * 数据收发测试.
 *
 * @WSController
 */
class TestController extends WebSocketController
{
    /**
     * 发送消息.
     *
     * @WSAction
     *
     * @WSRoute({"action"="send"})
     */
    public function send($data): array
    {
        $address = $this->frame->getClientAddress();
        $message = '[' . $address->getAddress() . ':' . $address->getPort() . ']: ' . $data->message;

        return [
            'success' => true,
            'data'    => $message,
            'mode'    => App::getApp()->getType(),
        ];
    }
}
