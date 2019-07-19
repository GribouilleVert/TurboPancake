<?php
namespace TurboPancake;

use DI\Container;
use TurboPancake\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class App
 * @package TurboPancake
 */
final class App {

    /**
     * Modules instanciés
     * @var array
     */
    private $module = [];

    /**
     * Router de l'application
     * @var Container
     */
    private $container;

    /**
     * App constructor.
     * @param ContainerInterface $container Conteneur de dépendances
     * @param array $modules Liste des modules a charger
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->module[] = $container->get($module);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception Si un callback est mal configuré
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        if (!is_null($parsedBody)
            AND array_key_exists('_method', $parsedBody)
            AND in_array($parsedBody['_method'], ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }

        $uri = $request->getUri()->getPath();
        if (!empty($uri) AND $uri[-1] === '/') {
            return new Response(301, ['Location' => substr($uri, 0, -1)]);
        }

        $route = $this->container->get(Router::class)->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }

        if ($this->container->has(RendererInterface::class)) {
            $renderer = $this->container->get(RendererInterface::class);
            $renderer->addGlobal('route', $route->getName());
        }

        $parameters = $route->getParams();
        $request = array_reduce(
            array_keys($parameters),
            function (ServerRequestInterface $request, $key) use ($parameters) {
                return $request->withAttribute($key, $parameters[$key]);
            },
            $request
        );

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
            throw new \Exception('Response type is invalid, expected string or ResponseInterface instance');
        }
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

}
