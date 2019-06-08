<?php
namespace Tests\TurboPancake\Modules;

use TurboPancake\Module;
use TurboPancake\Router;
class CallStringModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/test', CallStringActions::class, 'test_module.response_object');
    }

}