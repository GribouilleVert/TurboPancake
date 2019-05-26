<?php
namespace Tests\Framework\Modules;

use Framework\Module;
use Framework\Router;
class CallStringModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/test', CallStringActions::class, 'test_module.response_object');
    }

}