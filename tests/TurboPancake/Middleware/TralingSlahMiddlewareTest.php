<?php

namespace Tests\TurboPancake\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Middlewares\TralingSlashMiddleware;

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
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->never())
            ->method('handle');

        $request = new ServerRequest('GET', '/abcdef/12345/');
        $response = $this->middleware->process($request, $handler);

        $this->assertEquals('/abcdef/12345', $response->getHeader('Location')[0]);
    }

    public function testIgnoreCorrectsURIs() {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $request = new ServerRequest('GET', '/abcdef/12345');

        $handler->expects($this->once())
            ->method('handle')
        ->with($this->callback(function (ServerRequestInterface $r) use ($request) {
            return $r === $request;
        }));

        $response = $this->middleware->process($request, $handler);
    }

}