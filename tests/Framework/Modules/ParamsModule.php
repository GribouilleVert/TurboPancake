<?php
namespace Tests\Framework\Modules;

use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class ParamsModule {

    public function __construct(Router $router)
    {
        $router->get('/test/{name}', [$this, 'index'], 'test_module.');
    }

    public function index(Request $request): string
    {
        return 'Salut ' . $request->getAttribute('name') . ' !';
    }

}
