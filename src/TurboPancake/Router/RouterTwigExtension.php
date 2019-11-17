<?php
namespace TurboPancake\Router;

use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Router;
use Twig\Extension\AbstractExtension;

final class RouterTwigExtension extends AbstractExtension {


    /**
     * @var Router
     */
    private $router;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction('path', [$this, 'generatePAth']),
            new \Twig\TwigFunction('is_path', [$this, 'isPath']),
            new \Twig\TwigFunction('is_subpath', [$this, 'isSubPath']),
        ];
    }

    public function generatePath(string $route, array $parameters = [], array $queryParameters = []): string
    {
        return $this->router->generateUri($route, $parameters, $queryParameters);
    }

    public function isPath(string $route, array $parameters = []): bool
    {
        $uri = $_SERVER['REQUEST_URI']??'/';
        if (isset($_SERVER['QUERY_STRING'])) {
            $uri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $uri);
        }
        $expectedUri = $this->router->generateUri($route, $parameters);
        return $expectedUri === $uri;
    }

    public function isSubPath(string $route, array $parameters = []): bool
    {
        $uri = $_SERVER['REQUEST_URI']??'/';
        $expectedUri = $this->router->generateUri($route, $parameters);
        return strpos($uri, $expectedUri) === 0;
    }
}
