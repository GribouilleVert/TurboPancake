<?php
namespace Tests\Framework;

use Framework\Renderer;
use Framework\Renderer\PHPRenderer;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\App;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class AppTest extends TestCase {

    /**
     * @var PHPRenderer
     */
    private $renderer;

    public function setUp(): void {
        $this->renderer = new PHPRenderer();
        $this->renderer->addPath(__DIR__ . '/views');
    }

    public function testRedirectTrailingSlash() {
        $app = new App();
        $request = new ServerRequest('GET', '/test/');
        $response = $app->run($request);

        $this->assertContains('/test', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testPageWithParametersInUrl() {
        $app = new App([
            Modules\ParamsModule::class
        ], [
            'renderer' => $this->renderer
        ]);

        $requestArticle = new ServerRequest('GET', '/test/Vasco');
        $responseArticle = $app->run($requestArticle);

        $this->assertStringContainsString('Salut Vasco !', (string)$responseArticle->getBody());
        $this->assertEquals(200, $responseArticle->getStatusCode());
    }

    public function testError404() {
        $app = new App();

        $request = new ServerRequest('GET', '/this-page-should-not-exist/if-it-does/it-s-weird');
        $response = $app->run($request);

        $this->assertStringContainsString('<h1>Erreur 404</h1>', (string)$response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testThrowExceptionOnWrongCallbackReturnType() {
        $app = new App([
            Modules\WrongModule::class
        ], [
            'renderer' => $this->renderer
        ]);

        $request = new ServerRequest('GET', '/trigger-error');

        $this->expectException(\Exception::class);
        $response = $app->run($request);
    }

    public function testStringToResponseConvertion() {
        $app = new App([
            Modules\StringModule::class
        ], [
            'renderer' => $this->renderer
        ]);

        $request = new ServerRequest('GET', '/test');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('Looks like a string !', (string)$response->getBody());
    }

    public function testClassicResponse() {
        $app = new App([
            Modules\ClassicModule::class
        ], [
            'renderer' => $this->renderer
        ]);

        $request = new ServerRequest('GET', '/test');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('Yep, ca marche !', (string)$response->getBody());
    }

}