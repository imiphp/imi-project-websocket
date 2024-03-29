<?php

declare(strict_types=1);

namespace ImiApp\Module\Test\ApiController;

use Imi\Aop\Annotation\Inject;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use ImiApp\Module\Test\Service\TestService;

/**
 * @Controller("/")
 */
class IndexController extends HttpController
{
    /**
     * @Inject
     */
    protected TestService $testService;

    /**
     * @Action
     *
     * @Route("/")
     *
     * @return array
     */
    public function index()
    {
        return [
            'data'  => $this->testService->getImi(),
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
            'data'  => 'api',
        ];
    }
}
