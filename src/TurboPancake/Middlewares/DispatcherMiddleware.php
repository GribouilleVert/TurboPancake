<?php
namespace TurboPancake\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Router\Route;

class DispatcherMiddleware implements MiddlewareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var $route Route
         */
        $route = $request->getAttribute(Route::class);
        if (is_null($route)) {
            return $handler->handle($request);
        }

        return $route->getMiddleware()->process($request, $handler);
    }
}
