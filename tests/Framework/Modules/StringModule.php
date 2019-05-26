<?php
namespace Tests\Framework\Modules;

use Framework\Module;
use Framework\Router;

class StringModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/test', function () {
            return 'Looks like a string !';
        }, 'string.trigger');
    }

}