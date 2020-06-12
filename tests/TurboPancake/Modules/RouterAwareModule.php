<?php
namespace Tests\TurboPancake\Modules;

use TurboPancake\Router\RouterAware;
use TurboPancake\Module;
use TurboPancake\Router\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class RouterAwareModule extends Module {

    /**
     * @var Router
     */
    private $router;

    use RouterAware;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function load(): void
    {
        $this->router->get('/gribouille-{color}', function(){}, 'test_module.land_url');

        $this->router->get('/301/{color}', function (Request $r) {
            return $this->permanentRedirect('test_module.land_url', ['color' => $r->getAttribute('color')]);
        }, 'test_module.301_redirect');

        $this->router->get('/302/{color}', function (Request $r) {
            return $this->temporaryRedirect('test_module.land_url', ['color' => $r->getAttribute('color')]);
        }, 'test_module.302_redirect');

    }

}