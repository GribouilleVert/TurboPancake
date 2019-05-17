<?php
namespace Tests\Framework;

use GuzzleHttp\Psr7\ServerRequest;
use Framework\App;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class AppTest extends TestCase {

    public function testRedirectTrailingSlash() {
        $app = new App();
        $request = new ServerRequest('GET', '/test/');
        $response = $app->run($request);

        $this->assertContains('/test', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testBlog() {
        $app = new App([
            \Haifunime\Blog\BlogModule::class
        ]);

        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);

        $this->assertStringContainsString('<h1>Bienvenue sur le blog !</h1>', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());

        $requestArticle = new ServerRequest('GET', '/blog/gribouille-violet');
        $responseArticle = $app->run($requestArticle);

        $this->assertStringContainsString('<h1>Bienvenue sur l\'article gribouille-violet !</h1>', (string)$responseArticle->getBody());
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
        ]);

        $request = new ServerRequest('GET', '/trigger-error');

        $this->expectException(\Exception::class);
        $response = $app->run($request);
    }

    public function testStringToResponseConvertion() {
        $app = new App([
            Modules\StringModule::class
        ]);

        $request = new ServerRequest('GET', '/test');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('Looks like a string !', (string)$response->getBody());
    }

    public function testClassicResponse() {
        $app = new App([
            Modules\ClassicModule::class
        ]);

        $request = new ServerRequest('GET', '/test');
        $response = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('Yep, ca marche !', (string)$response->getBody());
    }

}