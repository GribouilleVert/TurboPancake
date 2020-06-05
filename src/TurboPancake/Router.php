<?php
namespace TurboPancake;

use Mezzio\Router\FastRouteRouter;
use Psr\Container\ContainerInterface;
use TurboPancake\Exceptions\SystemException;
use TurboPancake\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Router\Route as InternalRoute;

/**
 * Class Router
 * @package TurboPancake
 * Enregistre et vérifie les routes.
 */
final class Router {

    private const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];

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
     * @throws SystemException
     */
    public function get(string $path, string $middleware, string $name)
    {
        $this->custom($path, ['GET'], $middleware, $name);
    }

    /**
     * Permet d'ajouter une URI en POST
     * @param string $path URI (avec paramètres ex: "/create/{id:[0-9]+}"
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     * @throws SystemException
     */
    public function post(string $path, string $middleware, ?string $name = null)
    {
        $this->custom($path, ['POST'], $middleware, $name);
    }

    /**
     * Permet d'ajouter une URI en PUT
     * @param string $path URI (avec paramètres ex: "/edit/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     * @throws SystemException
     */
    public function put(string $path, string $middleware, ?string $name = null)
    {
        $this->custom($path, ['PUT'], $middleware, $name);
    }

    /**
     * Permet d'ajouter une URI en PATCH
     * @param string $path URI (avec paramètres ex: "/edit/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     * @throws SystemException
     */
    public function patch(string $path, string $middleware, ?string $name = null)
    {
        $this->custom($path, ['PATCH'], $middleware, $name);
    }

    /**
     * Permet d'ajouter une URI en DELETE
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     * @throws SystemException
     */
    public function delete(string $path, string $middleware, ?string $name = null)
    {
        $this->custom($path, ['DELETE'], $middleware, $name);
    }

    /**
     * Permet d'ajouter une URI en OPTIONS
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     * @throws SystemException
     */
    public function options(string $path, string $middleware, ?string $name = null)
    {
        $this->custom($path, ['OPTIONS'], $middleware, $name);
    }

    /**
     * Permet d'ajouter une URI en HEAD
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string|null $name Nom de la route
     * @throws SystemException
     */
    public function head(string $path, string $middleware, ?string $name = null)
    {
        $this->custom($path, ['HEAD'], $middleware, $name);
    }

    /**
     * Permet d'ajouter une URI en GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string $middleware Middleware a appeler.
     * @param string $name Nom de la route
     * @throws SystemException
     */
    public function all(string $path, string $middleware, string $name)
    {
        $this->custom($path, self::METHODS, $middleware, $name);
    }

    /**
     * Permet d'ajouter une URI avec les méthodes de sont choix
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param array $methods Liste des méthodes
     * @param string $middleware Middleware a appeler.
     * @param string $name Nom de la route
     * @throws SystemException
     */
    public function custom(string $path, array $methods, string $middleware, ?string $name = null)
    {
        foreach ($methods as $method) {
            if (!in_array($method, self::METHODS)) {
                throw new SystemException("Unsupported method '$method'.");
            }
        }

        if (in_array('GET', $methods) AND is_null($name)) {
            throw new SystemException('A route name is required when the GET method is present.');
        }

        $this->internalRouter->addRoute(new InternalRoute(
            $path,
            $this->container->get($middleware),
            $methods,
            $name
        ));
    }


    /**
     * Permet d'ajouter les URIs Create, Read, Update et Delete (CRUD)
     * @param string $basePath URI de base
     * @param string $middleware Middleware a appeler.
     * @param string|null $baseName Nom de base des routes
     * @throws SystemException
     */
    public function crud(string $basePath, string $middleware, string $baseName)
    {
        $this->get("$basePath", $middleware, $baseName . '.index');
        $this->get("$basePath/new", $middleware, $baseName . '.create');
        $this->post("$basePath/new", $middleware);
        $this->get("$basePath/{id}", $middleware, $baseName . '.edit');
        $this->patch("$basePath/{id}", $middleware);
        $this->delete("$basePath/{id}", $middleware, $baseName . '.delete');
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
