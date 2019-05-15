<?php
namespace Tests\Haifunime;

use GuzzleHttp\Psr7\ServerRequest;
use Haifunime\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase {

    function testRedirectTrailingSlash() {
        $app = new App();
        $request = new ServerRequest('GET', '/test/');
        $response = $app->run($request);

        $this->assertContains('/test', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }
}