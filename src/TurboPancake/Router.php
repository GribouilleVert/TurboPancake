<?php
namespace TurboPancake;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use TurboPancake\Router\CallableMiddleware;
use TurboPancake\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Class Router
 * @package TurboPancake
 * Enregistre et vérifie les routes.
 */
final class Router {

    private $internalRouter;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Router constructor.
     * @param ContainerInterface $container
     * @param string|null $cache
     */
    public function __construct(ContainerInterface $container, ?string $cache = null)
    {
        $this->internalRouter = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => !is_null($cache),
            FastRouteRouter::CONFIG_CACHE_FILE => $cache
        ]);
        $this->container = $container;
    }

    /**
     * Permet d'ajouter une URI en GET
     * @param string $path URI (avec paramètres ex: "/blog/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string $name Nom de la route
     */
    public function get(string $path, string $middleware, string $name)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $this->container->get($middleware),
            ['GET'],
            $name
        ));
    }

    /**
     * Permet d'ajouter une URI en POST
     * @param string $path URI (avec paramètres ex: "/create/{id:[0-9]+}"
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     */
    public function post(string $path, string $middleware, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $this->container->get($middleware),
            ['POST'],
            $name
        ));
    }

    /**
     * Permet d'ajouter une URI en PUT
     * @param string $path URI (avec paramètres ex: "/edit/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     */
    public function put(string $path, string $middleware, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $this->container->get($middleware),
            ['PUT'],
            $name
        ));
    }

    /**
     * Permet d'ajouter une URI en PATCH
     * @param string $path URI (avec paramètres ex: "/edit/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     */
    public function patch(string $path, string $middleware, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $this->container->get($middleware),
            ['PATCH'],
            $name
        ));
    }

    /**
     * Permet d'ajouter une URI en DELETE
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     */
    public function delete(string $path, string $middleware, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $this->container->get($middleware),
            ['DELETE'],
            $name
        ));
    }

    /**
     * Permet d'ajouter une URI en OPTIONS
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     */
    public function options(string $path, string $middleware, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $this->container->get($middleware),
            ['OPTIONS'],
            $name
        ));
    }

    /**
     * Permet d'ajouter une URI en HEAD
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     */
    public function head(string $path, string $middleware, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $this->container->get($middleware),
            ['HEAD'],
            $name
        ));
    }

    /**
     * Permet d'ajouter une URI en GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string $name Nom de la route
     */
    public function all(string $path, string $middleware, string $name)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $this->container->get($middleware),
            ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'],
            $name
        ));
    }

    /**
     * Permet d'ajouter les URIs Create, Read, Update et Delete (CRUD)
     * @param string $basePath URI de base
     * @param string $middleware Middleware a appeler.
     * @param string|null $baseName Nom de base des routes
     */
    public function crud(string $basePath, string $middleware, string $baseName)
    {

        $this->get("$basePath", $middleware, $baseName . '.index');
        $this->get("$basePath/new", $middleware, $baseName . '.create');
        $this->post("$basePath/new", $middleware);
        $this->get("$basePath/{id:\d+}", $middleware, $baseName . '.edit');
        $this->patch("$basePath/{id:\d+}", $middleware);
        $this->delete("$basePath/{id:\d+}", $middleware, $baseName . '.delete');
    }

    /**
     * Permet de chercher la route qui correspond a l'objet Request
     * @param ServerRequestInterface $request la requête a tester
     * @return Route|null L'instance de Route qui correspond a l'URI, null si aucune correspondance n'est trouvée
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->internalRouter->match($request);

        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedRoute()->getMiddleware(),
                $result->getMatchedParams()
            );
        }
        return null;
    }

    /**
     * Génère une URI
     * @param string $name Nom de la route
     * @param array $parameters Paramètres
     * @param array $queryParameters Paramètres GET (?var=val)
     * @return string URI Générée
     */
    public function generateUri(string $name, array $parameters = [], array $queryParameters = []): ?string
    {
        $uri = $this->internalRouter->generateUri($name, $parameters);
        if (!empty($queryParameters)) {
            return $uri . '?' . http_build_query($queryParameters);
        }
        return $uri;
    }
}
