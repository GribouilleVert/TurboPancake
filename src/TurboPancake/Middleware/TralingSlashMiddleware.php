<?php
namespace TurboPancake\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TralingSlashMiddleware {

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) AND $uri[-1] === '/') {
            return new Response(301, ['Location' => substr($uri, 0, -1)]);
        }
        return $next($request);
    }

}
