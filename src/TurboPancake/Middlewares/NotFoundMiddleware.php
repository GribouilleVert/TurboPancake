<?php
namespace TurboPancake\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Router\Route;

class NotFoundMiddleware {

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return new Response(404, [], '<h1>Erreur 404</h1>');
    }

}
