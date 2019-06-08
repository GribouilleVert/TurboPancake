<?php
namespace TurboPancake\Router;

/**
 * Class Route
 * @package TurboPancake\Router
 * Représente une route
 */
class Route {
    /**
     * @var string Nom de la route
     */
    private $name;

    /**
     * @var callable Callback a appeler lors d'un match
     */
    private $callback;

    /**
     * @var array Paramètres
     */
    private $parameters;

    /**
     * Route constructor.
     * @param string $name
     * @param string|callable $callback
     * @param array $parameters
     */
    public function __construct(string $name, $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
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
     * @return string|callable
     */
    public function getCallback()
    {
        return $this->callback;
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
