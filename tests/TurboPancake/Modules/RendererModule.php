<?php
namespace Tests\TurboPancake\Modules;

use TurboPancake\Module;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use Psr\Http\Message\ServerRequestInterface;

class RendererModule extends Module {

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'test');
        $router->get('/test', function () use ($renderer) {
            return $renderer->render('@test/index');
        }, 'test_module.rendered_view');
    }

}