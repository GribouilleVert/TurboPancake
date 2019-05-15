<?php
namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Class Router
 * @package Framework
 * Enregistre et vérifie les routes.
 */
class Router {

    private $router;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * @param string $path
     * @param callable $callback
     * @param string $name
     */
    public function get(string $path, callable $callback, string $name)
    {
        $this->router->addRoute(new ZendRoute($path, $callback, ['GET'], $name));
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);

        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        }
        return null;
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return string|null
     */
    public function generateUri(string $name, array $parameters): ?string
    {
        return $this->router->generateUri($name, $parameters);
    }

}
