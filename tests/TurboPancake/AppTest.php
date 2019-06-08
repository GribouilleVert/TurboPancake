<?php
namespace Tests\TurboPancake;

use DI\Container;
use TurboPancake\Renderer\PHPRendererFactory;
use TurboPancake\Renderer\RendererInterface;
use GuzzleHttp\Psr7\ServerRequest;
use TurboPancake\App;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class AppTest extends TestCase {

    /**
     * @var Container
     */
    private $container;

    public function setUp(): void {
        $builder = new \DI\ContainerBuilder();
        $this->container = $builder->build();
    }

    public function testRedirectTrailingSlash() {
        $app = new App($this->container);
        $request = new ServerRequest('GET', '/test/');
        $response = $app->run($request);

        $this->assertContains('/test', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testPageWithParametersInUrl() {
        $app = new App($this->container, [
            Modules\ParamsModule::class
        ]);

        $requestArticle = new ServerRequest('GET', '/test/Vasco');
        $responseArticle = $app->run($requestArticle);

        $this->assertStringContainsString('Salut Vasco !', (string)$responseArticle->getBody());
        $this->assertEquals(200, $responseArticle->getStatusCode());
    }

    public function testMethodInForm() {
        $app = new App($this->container, [
            Modules\DeleteMethodTesterModule::class
        ]);

        $request = new ServerRequest('POST', '/test');
        $request = $request->withParsedBody(['_method' => 'DELETE']);
        $response = $app->run($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testError404() {
        $app = new App($this->container);

        $request = new ServerRequest('GET', '/this-page-should-not-exist/if-it-does/it-s-weird');
        $response = $app->run($request);

        $this->assertStringContainsString('<h1>Erreur 404</h1>', (string)$response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testThrowExceptionOnWrongCallbackReturnType() {
        $app = new App($this->container, [
            Modules\WrongModule::class
        ]);

        $request = new ServerRequest('GET', '/trigger-error');

        $this->expectException(\Exception::class);
        $response = $app->run($request);
    }

    public function testStringToResponseConversion() {
        $app = new App($this->container, [
            Modules\StringModule::class
        ]);

        $request = new ServerRequest('GET', '/test');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('Looks like a string !', (string)$response->getBody());
    }

    public function testStringCallback() {
        $app = new App($this->container, [
            Modules\CallStringModule::class
        ]);

        $request = new ServerRequest('GET', '/test');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('Yep, ca marche !', (string)$response->getBody());
    }

    public function testClassicResponse() {
        $app = new App($this->container, [
            Modules\ClassicModule::class
        ]);

        $request = new ServerRequest('GET', '/test');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('Yep, ca marche !', (string)$response->getBody());
    }

    public function testRendererGlobalsAdded() {
        $this->container->set('views.path', null);
        $this->container->set(RendererInterface::class, \DI\Factory(PHPRendererFactory::class));

        $app = new App($this->container, [
            Modules\RendererModule::class
        ]);

        $request = (new ServerRequest('GET', '/test'))
            ->withQueryParams(['a' => 123]);
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('test_module.rendered_view - 123', (string)$response->getBody());
    }

}