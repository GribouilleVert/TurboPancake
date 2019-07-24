<?php
namespace TurboPancake;

use DI\Container;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use mysql_xdevapi\Exception;
use TurboPancake\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class App
 * @package TurboPancake
 */
final class App implements DelegateInterface {

    /**
     * Modules
     * @var string[]
     */
    private $modules = [];

    /**
     * Middlewares
     * @var string[]
     */
    private $middlewares;

    /**
     * Router de l'application
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $containerDefinition;

    /**
     * @var int index des middlewares
     */
    private $index = 0;

    /**
     * App constructor.
     * @param mixed $containerDefinitions Definitions du conteneur d'injection de dépandendances
     * @param array $modules
     */
    public function __construct($containerDefinitions, array $modules = [])
    {
        $this->containerDefinition = $containerDefinitions;
        $this->modules = $modules;
    }

    /**
     * Ajoute un module
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Ajoute un middleware
     * @param string $middware
     * @return App
     */
    public function pipe(string $middware): self
    {
        $this->middlewares[] = $middware;
        return $this;
    }

    /**
     * Execute les middlewares
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception('None of the middlewares catched de request.');
        } elseif (is_callable($middleware)) {
            return $middleware($request, [$this, 'process']);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
        throw new \Exception('Invalid middleware type');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception Si un callback est mal configuré
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }

        return $this->process($request);
    }

    /**
     * @return ContainerInterface
     * @throws \Exception
     */
    public function getContainer(): ContainerInterface
    {
        if (!$this->container instanceof ContainerInterface) {
            $builder = new \DI\ContainerBuilder();
            $builder->addDefinitions($this->containerDefinition);
            foreach ($this->modules as $module) {
                if (!is_null($module::DEFINITIONS)) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }

        return $this->container;
    }

    /**
     * @return MiddlewareInterface|callable|null
     * @throws \Exception
     */
    private function getMiddleware(): ?object
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->getContainer()->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }

}
