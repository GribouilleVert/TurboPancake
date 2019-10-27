<?php
namespace TurboPancake\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TralingSlashMiddleware implements MiddlewareInterface {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if ($uri !== '/' AND !empty($uri) AND $uri[-1] === '/') {
            return new Response(301, ['Location' => substr($uri, 0, -1)]);
        }
        return $handler->handle($request);
    }

}
