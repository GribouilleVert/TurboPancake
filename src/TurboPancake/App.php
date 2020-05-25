<?php
namespace TurboPancake;

use DI\Container;
use DI\DependencyException;
use Exception;
use PHPUnit\Framework\Warning;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Database\Sprinkler;
use TurboPancake\Exceptions\SystemException;
use TurboPancake\Middlewares\RoutedMiddleware;
use TurboPancake\Renderer\PHPRenderer;
use TurboPancake\Renderer\RendererInterface;

/**
 * Class App
 * @package TurboPancake
 */
final class App implements RequestHandlerInterface {

    public const SEVERITY_HIGH = 0x1;
    public const SEVERITY_MEDIUM = 0x2;
    public const SEVERITY_LOW = 0x2;
    public const SEVERITY_CRITICAL = 0x3;

    /**
     * Modules
     * @var string[]
     */
    private $modules = [];

    /**
     * Middlewares
     * @var string[]
     */
    private $middlewares = [];

    /**
     * Router de l'application
     * @var Container
     */
    private $container;

    /**
     * @var string|array
     */
    private $containerDefinitions;

    /**
     * @var int index des middlewares
     */
    private $index = 0;

    /**
     * App constructor.
     * @param mixed $containerDefinitions Definitions du conteneur d'injection de dependence
     * @param array $modules
     * @param array $middlewares
     */
    public function __construct($containerDefinitions, array $modules = [], array $middlewares = [])
    {
        $this->containerDefinitions = $containerDefinitions;
        $this->modules = $modules;
        $this->middlewares = $middlewares;
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
     * Renvoie la liste des modules
     * @return string[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Ajoute un middleware
     * @param string|MiddlewareInterface $middlware
     * @param string|null $path
     * @return App
     */
    public function trough($middlware, ?string $path = null): self
    {
        if (is_null($path)) {
            $this->middlewares[] = $middlware;
        } else {
            $this->middlewares[] = new RoutedMiddleware($path, $middlware, $this->getContainer());
        }
        return $this;
    }

    /**
     * Execute les middlewares
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception('None of the middlewares catched de request.');
        } elseif ($middleware instanceof MiddlewareInterface) {
            try {
                return $middleware->process($request, $this);
            } catch (SystemException $exception) {
                $this->error($exception);
            }
        }
        throw new \Exception('Invalid middleware type');
    }

    /**
     * Lance de traitement global - ENTRY POINT
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception Si un middleware est mal configuré
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $loadedModules = [];
        $errors = [];
        foreach ($this->modules as $module) {
            /**
             * @var $module Module
             */
            $moduleName = $module;
            $module = $this->getContainer()->get($module);
            $moduleDependencies = $module->getModuleDependencies();
            $middlewareDependencies = $module->getMiddlewareDependencies();

            foreach ($moduleDependencies as $moduleDependency) {
                if (!in_array($moduleDependency, $this->modules)) {
                    $errors[] = new SystemException(
                        'Unable to load module ' . $moduleName .
                        ' beacause the required module ' . $moduleDependency . ' is not present',
                        self::SEVERITY_LOW
                    );
                    continue 2;
                }
            }

            foreach ($middlewareDependencies as $middlewareDependency) {
                if (!in_array($middlewareDependency, $this->middlewares)) {
                    foreach ($this->middlewares as $middleware) {
                        if ($middleware instanceof RoutedMiddleware) {
                            if ($middlewareDependency === $middleware->getMiddleware()) {
                                continue 2;
                            }
                        }
                    }
                    $errors[] = new SystemException(
                        'Unable to load module ' . $moduleName .
                        ' beacause the required middleware ' . $middlewareDependency . ' is not present',
                        self::SEVERITY_LOW
                    );
                    continue 2;
                }
            }
            $loadedModules[] = $moduleName;
            $module->load();
        }

        $applicationDetails = [
            'version' => 'TurboPancake 2.0',
            'container' => get_class($this->container),
            'renderer' => get_class($this->container->get(RendererInterface::class)),
        ];

        $this->container->set('turbopancake.details', $applicationDetails);
        $this->container->set('turbopancake.modules', $this->modules);
        $this->container->set('turbopancake.loadedModules', $loadedModules);
        $this->container->set('turbopancake.middlewares', $this->middlewares);

        if ($this->container->has(RendererInterface::class)) {
            $this->container->get(RendererInterface::class)->addGlobal('modules', $loadedModules);
            $this->container->get(RendererInterface::class)->addGlobal('middlewares', $this->middlewares);
            $this->container->get(RendererInterface::class)->addGlobal('applicationDetails', $applicationDetails);
        }

        if (!empty($errors)) {
            $this->error(...$errors);
        }

        return $this->handle($request);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if (!$this->container instanceof ContainerInterface) {
            $builder = new \DI\ContainerBuilder();
            $env = getenv('ENV') ?: 'development'; //TODO:  Set back to `production`
            if ($env === 'production') {
                $builder->enableDefinitionCache();
                //                $builder->enableCompilation('tmp'); #Actually buged, TODO: Check is the bug is fixed
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }

            if (is_string($this->containerDefinitions)) {
                $builder->addDefinitions($this->containerDefinitions);
            } elseif (is_array($this->containerDefinitions)) {
                foreach ($this->containerDefinitions as $containerDefinition) {
                    $builder->addDefinitions($containerDefinition);
                }
            } else {
                die('Unable to build container: Invalid Container definition type');
            }

            foreach ($this->modules as $module) {
                if (!is_null($module::DEFINITIONS)) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            try {
                $this->container = $builder->build();
                Sprinkler::setContainer($this->container);
            } catch (\Exception $e) {
                die('Unable to build container: ' . $e->getMessage());
            }
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
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->getContainer()->get($this->middlewares[$this->index]);
            } elseif ($this->middlewares[$this->index] instanceof MiddlewareInterface) {
                $middleware = $this->middlewares[$this->index];
            } else {
                throw new Exception('Invalid middleware type, only strings and instances of MiddlewareInterface are accepted.');
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }

    private function error(...$exceptions)
    {
        $renderer = new PHPRenderer(__DIR__ . '/views');
        echo $renderer->render('error', [
            'exceptions' => $exceptions,
            'details' => $this->getContainer()->get('turbopancake.details'),
            'modules' => $this->getContainer()->get('turbopancake.modules'),
            'loaded_modules' => $this->getContainer()->get('turbopancake.loadedModules'),
            'middlewares' => $this->getContainer()->get('turbopancake.middlewares'),
        ]);
        die;
    }
}
