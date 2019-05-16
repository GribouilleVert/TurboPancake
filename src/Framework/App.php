<?php
namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class App
 * @package Framework
 */
class App {

    /**
     * Modules instanciés
     * @var array
     */
    private $module = [];

    /**
     * Router de l'application
     * @var Router
     */
    private $router;

    /**
     * App constructor.
     * @param array $modules Liste des modules a charger
     */
    public function __construct(array $modules = [])
    {
        $this->router = new Router();
        foreach ($modules as $module)
            $this->module[] = new $module($this->router);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception Si un callback est mal configuré
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) AND $uri[-1] === '/') {
            return new Response(301, ['Location' => substr($uri, 0, -1)]);
        }

        $route = $this->router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }

        $parameters = $route->getParams();
        $request = array_reduce(array_keys($parameters), function (ServerRequestInterface $request, $key) use ($parameters) {
            return $request->withAttribute($key, $parameters[$key]);
        }, $request);

        $response = call_user_func_array($route->getCallback(), [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('Response type is invalid, expected string or ResponseInterface instance');
        }
    }

}
