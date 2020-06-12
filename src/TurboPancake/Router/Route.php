<?php
namespace TurboPancake\Router;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Class Route
 * @package TurboPancake\Router
 * Représente une route
 */
final class Route {
    /**
     * @var string Nom de la route
     */
    private $name;

    /**
     * @var MiddlewareInterface Callback a appeler lors d'un match
     */
    private $middleware;

    /**
     * @var array Paramètres
     */
    private $parameters;

    /**
     * Route constructor.
     * @param string $name
     * @param MiddlewareInterface $middleware
     * @param array $parameters
     */
    public function __construct(string $name, MiddlewareInterface $middleware, array $parameters)
    {
        $this->name = $name;
        $this->middleware = $middleware;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return MiddlewareInterface
     */
    public function getMiddleware(): MiddlewareInterface
    {
        return $this->middleware;
    }

    /**
     * Récupère les paramètre de l'URL
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
