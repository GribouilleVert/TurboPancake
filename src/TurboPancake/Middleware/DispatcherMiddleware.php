<?php
namespace TurboPancake\Middleware;

use GuzzleHttp\Psr7\Response;
use function PHPSTORM_META\type;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Router;
use TurboPancake\Router\Route;

class DispatcherMiddleware {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $request->getAttribute(Route::class);
        if (is_null($route)) {
            return $next($request);
        }

        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }

        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception(
                'Response type is invalid, expected string or ResponseInterface instance, got ' . gettype($response)
            );
        }
    }

}
