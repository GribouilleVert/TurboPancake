<?php
namespace Tests\Framework\Modules;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
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