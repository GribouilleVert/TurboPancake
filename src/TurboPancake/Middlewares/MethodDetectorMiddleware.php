<?php
namespace TurboPancake\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodDetectorMiddleware implements MiddlewareInterface {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        if (!is_null($parsedBody)
            AND array_key_exists('_method', $parsedBody)
            AND in_array($parsedBody['_method'], ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        return $handler->handle($request);
    }

}
