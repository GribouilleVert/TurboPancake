<?php
namespace Framework\Router;

use Framework\Router;
use Twig\Extension\AbstractExtension;

class RouterTwigExtension extends AbstractExtension
{

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction('path', [$this, 'generatePAth']),
        ];
    }

    public function generatePath(string $route, array $parameters = [], array $queryParameters = []): string
    {
        return $this->router->generateUri($route, $parameters, $queryParameters);
    }

}
