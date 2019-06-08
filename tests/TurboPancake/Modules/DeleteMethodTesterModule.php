<?php
namespace Tests\TurboPancake\Modules;

use TurboPancake\Module;
use TurboPancake\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class DeleteMethodTesterModule extends Module {

    public function __construct(Router $router)
    {
        $router->delete('/test', function () {
            return new Response(200);
        }, 'test_module.response_object');
    }

}