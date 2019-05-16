<?php
namespace Tests\Framework\Modules;

use Framework\Router;

class WrongModule {

    public function __construct(Router $router)
    {
        $router->get('/trigger-error', function () {
            return new \stdClass();
        }, 'error.trigger');
    }

}