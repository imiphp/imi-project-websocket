<?php
namespace ImiApp\WebSocketServer\HttpController;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;

/**
 * @Controller("/")
 */
class IndexController extends HttpController
{
    /**
     * @Action
     * @Route("/")
     *
     * @return array
     */
    public function index()
    {
        return [
            'data'  =>  'index',
        ];
    }

    /**
     * @Action
     *
     * @return array
     */
    public function api()
    {
        return [
            'data'  =>  'api',
        ];
    }

}
