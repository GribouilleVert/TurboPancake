<?php
namespace Tests\Framework\Modules;

use Framework\Router;
use GuzzleHttp\Psr7\Response;

class ClassicModule {

    public function __construct(Router $router)
    {
        $router->get('/test', function () {
            return new Response(200, [], 'Yep, ca marche !');
        }, 'response.trigger');
    }

}