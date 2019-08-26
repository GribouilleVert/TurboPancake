<?php

namespace Tests\TurboPancake\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Middlewares\Exceptions\CsrfException;
use TurboPancake\Middlewares\CsrfMiddleware;

class CsrfMiddlewareTest extends TestCase {

    /**
     * @var CsrfMiddleware
     */
    private $middleware;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $delegate;

    /**
     * @var array
     */
    private $session;

    public function setUp(): void
    {
        $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session);
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $this->delegate = $delegate;
    }

    public function testLetGetMethodPass()
    {
        $this->delegate->expects($this->once())
            ->method('handle')
            ->willReturn(new Response);

        $request = new ServerRequest('GET', '/abcdef/12345');
        $this->middleware->process($request, $this->delegate);
    }

    public function testLetPostMethodPassWhenThereIsNoBody()
    {
        $this->delegate->expects($this->once())
            ->method('handle')
            ->willReturn(new Response);

        $request = new ServerRequest('POST', '/abcdef/12345');
        $this->middleware->process($request, $this->delegate);
    }

    public function testLetPostMethodPassWhenCsrfIsCorrect()
    {
        $this->delegate->expects($this->once())
            ->method('handle')
            ->willReturn(new Response);

        $token = $this->middleware->makeToken();

        $request = new ServerRequest('POST', '/abcdef/12345');
        $request = $request->withParsedBody(['a' => 1, '_csrf' => $token]);

        $this->middleware->process($request, $this->delegate);
    }

    public function testBlockPostMethodWhenCsrfIsAbsent()
    {
        $this->delegate->expects($this->never())
            ->method('handle')
            ->willReturn(new Response);

        $request = new ServerRequest('POST', '/abcdef/12345');
        $request = $request->withParsedBody(['a' => 1]);

        $this->expectException(CsrfException::class);
        $this->middleware->process($request, $this->delegate);
    }

    public function testBlockPostMethodWhenCsrfIsInvalid()
    {
        $this->delegate->expects($this->never())
            ->method('handle')
            ->willReturn(new Response);

        $request = new ServerRequest('POST', '/abcdef/12345');
        $request = $request->withParsedBody(['a' => 1, '_csfr' => 'invalid']);

        $this->expectException(CsrfException::class);
        $this->middleware->process($request, $this->delegate);
    }

    public function testBlockPostMethodWhenCsrfTokenIsUsedTwice()
    {
        $this->delegate->expects($this->once())
            ->method('handle')
            ->willReturn(new Response);

        $token = $this->middleware->makeToken();

        $request = new ServerRequest('POST', '/abcdef/12345');
        $request = $request->withParsedBody(['a' => 1, '_csrf' => $token]);

        $this->middleware->process($request, $this->delegate);

        $this->expectException(CsrfException::class);
        $this->middleware->process($request, $this->delegate);
    }

    public function testLimitTokenCount()
    {
        for ($i = 0; $i < 100; $i++) {
            $token = $this->middleware->makeToken();
        }
        $this->assertCount(15, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][14]);
    }

}