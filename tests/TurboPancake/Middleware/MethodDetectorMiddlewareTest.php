<?php

namespace Tests\TurboPancake\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use TurboPancake\Middleware\MethodDetectorMiddleware;

class MethodDetectorMiddlewareTest extends TestCase {

    /**
     * @var MethodDetectorMiddleware
     */
    private $middleware;

    public function setUp(): void
    {
        $this->middleware = new MethodDetectorMiddleware();
    }

    public function testValidMethodIsChanged() {
        $request = new ServerRequest('GET', '/abcdef/12345/');
        $request = $request->withParsedBody(['_method' => 'DELETE']);
        ($this->middleware)($request, function($request){
            $this->assertEquals('DELETE', $request->getMethod());
        });
    }

    public function testWrongMethodIsignored() {
        $request = new ServerRequest('GET', '/abcdef/12345/');
        $request = $request->withParsedBody(['_method' => 'Yolo']);
        ($this->middleware)($request, function($request){
            $this->assertEquals('GET', $request->getMethod());
        });
    }

}