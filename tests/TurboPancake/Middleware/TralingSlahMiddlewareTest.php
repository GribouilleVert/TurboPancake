<?php

namespace Tests\TurboPancake\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use TurboPancake\Middleware\TralingSlashMiddleware;

class TralingSlahMiddlewareTest extends TestCase {

    /**
     * @var TralingSlashMiddleware
     */
    private $middleware;

    public function setUp(): void
    {
        $this->middleware = new TralingSlashMiddleware();
    }

    public function testRemoveTrailingSlash() {
        $request = new ServerRequest('GET', '/abcdef/12345/');
        $response = ($this->middleware)($request, function($request){
            $this->fail('Next should never be called');
        });

        $this->assertEquals('/abcdef/12345', $response->getHeader('Location')[0]);
    }

    public function testIgnoreCorrectsURIs() {
        $request = new ServerRequest('GET', '/abcdef/12345');
        $response = ($this->middleware)($request, function($r) use ($request) {
            $this->assertEquals($request, $r);
        });
    }

}