<?php
namespace TurboPancake\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Router;

class RouterMiddleware {

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $this->router->match($request);
        if (is_null($route)) {
            return $next($request);
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
        return $next($request);
    }

}
