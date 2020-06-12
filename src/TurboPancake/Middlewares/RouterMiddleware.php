<?php
namespace TurboPancake\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Router\Router;

class RouterMiddleware implements MiddlewareInterface {

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->match($request);
        if (is_null($route)) {
            return $handler->handle($request);
        }

        $parameters = $route->getParams();
        $request = array_reduce(
            array_keys($parameters),
            function (ServerRequestInterface $request, $key) use ($parameters) {
                return $request->withAttribute($key, $parameters[$key]);
            },
            $request
        );
        $request = $request->withAttribute(get_class($route), $route);
        return $handler->handle($request);
    }
}
