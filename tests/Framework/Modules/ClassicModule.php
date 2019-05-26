<?php
namespace Tests\Framework\Modules;

use Framework\Module;
use Framework\Router;
use GuzzleHttp\Psr7\Response;

class ClassicModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/test', function () {
            return new Response(200, [], 'Yep, ca marche !');
        }, 'test_module.response_object');
    }

}