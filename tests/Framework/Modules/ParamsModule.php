<?php
namespace Tests\Framework\Modules;

use Framework\Module;
use Framework\Router;
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
