<?php
namespace Tests\TurboPancake\Actions;

use DI\Container;
use TurboPancake\Renderer\PHPRendererFactory;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router\Router;
use GuzzleHttp\Psr7\ServerRequest;
use TurboPancake\App;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Tests\TurboPancake\Modules\RouterAwareModule;

class RouterAwareTest extends TestCase {

    /**
     * @var Container
     */
    private $container;

    public function setUp(): void {
        $builder = new \DI\ContainerBuilder();
        $this->container = $builder->build();
    }

    public function test301Redirection() {
        $app = (new App([], [
            RouterAwareModule::class
        ]))->trough(\TurboPancake\Middlewares\RouterMiddleware::class)
            ->trough(\TurboPancake\Middlewares\DispatcherMiddleware::class);

        $request = new ServerRequest('GET', '/301/vert');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('/gribouille-vert', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function test302Redirection() {
        $app = (new App([], [
            RouterAwareModule::class
        ]))->trough(\TurboPancake\Middlewares\RouterMiddleware::class)
            ->trough(\TurboPancake\Middlewares\DispatcherMiddleware::class);

        $request = new ServerRequest('GET', '/302/violet');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('/gribouille-violet', $response->getHeader('Location'));
        $this->assertEquals(302, $response->getStatusCode());
    }

}