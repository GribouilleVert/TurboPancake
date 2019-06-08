<?php
namespace Tests\TurboPancake\Modules;

use TurboPancake\Module;
use TurboPancake\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class ParamsModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/test/{name}', [$this, 'index'], 'test_module.');
    }

    public function index(Request $request): string
    {
        return 'Salut ' . $request->getAttribute('name') . ' !';
    }

}
