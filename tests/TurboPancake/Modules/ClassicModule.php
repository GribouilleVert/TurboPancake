<?php
namespace Tests\TurboPancake\Modules;

use TurboPancake\Module;
use TurboPancake\Router;
use GuzzleHttp\Psr7\Response;

class ClassicModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/test', function () {
            return new Response(200, [], 'Yep, ca marche !');
        }, 'test_module.response_object');
    }

}