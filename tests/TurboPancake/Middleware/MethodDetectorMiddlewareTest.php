<?php

namespace Tests\TurboPancake\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\Utils\RequestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Middlewares\CsrfMiddleware;
use TurboPancake\Middlewares\MethodDetectorMiddleware;

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
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (ServerRequestInterface $request) {
                return $request->getMethod() === 'DELETE';
            }));

        $request = new ServerRequest('GET', '/abcdef/12345/');
        $request = $request->withParsedBody(['_method' => 'DELETE']);

        $this->middleware->process($request, $handler);
    }

    public function testWrongMethodIsignored() {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (ServerRequestInterface $request) {
                return $request->getMethod() === 'GET';
            }));

        $request = new ServerRequest('GET', '/abcdef/12345/');
        $request = $request->withParsedBody(['_method' => 'Yolo']);

        $this->middleware->process($request, $handler);
    }

}