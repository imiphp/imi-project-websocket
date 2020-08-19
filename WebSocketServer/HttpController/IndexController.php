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
     * @return void
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
     * @return void
     */
    public function api()
    {
        return [
            'data'  =>  'api',
        ];
    }

}
