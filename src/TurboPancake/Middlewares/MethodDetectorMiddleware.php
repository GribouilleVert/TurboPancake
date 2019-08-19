<?php
namespace TurboPancake\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MethodDetectorMiddleware {

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $parsedBody = $request->getParsedBody();
        if (!is_null($parsedBody)
            AND array_key_exists('_method', $parsedBody)
            AND in_array($parsedBody['_method'], ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        return $next($request);
    }

}
