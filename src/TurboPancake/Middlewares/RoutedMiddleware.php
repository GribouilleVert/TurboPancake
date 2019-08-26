<?php
namespace TurboPancake\Middlewares;

use DI\Definition\Exception\InvalidDefinition;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutedMiddleware implements MiddlewareInterface {

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $middleware;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(string $path, string $middleware, ContainerInterface $container)
    {
        $this->path = $path;
        $this->middleware = $middleware;
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $currentPath = $request->getUri()->getPath();
        if (strpos($currentPath, $this->path) === 0) {
            try {
                return $this->container->get($this->middleware)->process($request, $handler);
            } catch (InvalidDefinition $e) {
                trigger_error(
                    'Unable to load middleware ' . $this->middleware . ' due to ContainerInterface::get() fail.',
                    E_USER_WARNING
                );
            }
        }
        return $handler->handle($request);
    }

    /**
     * @return string
     */
    public function getMiddleware(): string
    {
        return $this->middleware;
    }

}
