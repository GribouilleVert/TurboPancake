<?php
namespace TurboPancake;

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
     * Router constructor.
     */
    public function __construct(?string $cache = null)
    {
        $this->internalRouter = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => !is_null($cache),
            FastRouteRouter::CONFIG_CACHE_FILE => $cache
        ]);
    }

    /**
     * Permet d'ajouter une URI en GET
     * @param string $path URI (avec paramètres ex: "/blog/{id:[0-9]+}")
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string $name Nom de la route
     */
    public function get(string $path, $callback, string $name)
    {
        $this->internalRouter->addRoute(new ZendRoute($path, $callback, ['GET'], $name));
    }

    /**
     * Permet d'ajouter une URI en POST
     * @param string $path URI (avec paramètres ex: "/create/{id:[0-9]+}")
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string|null $name Nom de la route
     */
    public function post(string $path, $callback, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute($path, $callback, ['POST'], $name));
    }

    /**
     * Permet d'ajouter une URI en PUT
     * @param string $path URI (avec paramètres ex: "/edit/{id:[0-9]+}")
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string|null $name Nom de la route
     */
    public function put(string $path, $callback, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute($path, $callback, ['PUT'], $name));
    }

    /**
     * Permet d'ajouter une URI en PATCH
     * @param string $path URI (avec paramètres ex: "/edit/{id:[0-9]+}")
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string|null $name Nom de la route
     */
    public function patch(string $path, $callback, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute($path, $callback, ['PATCH'], $name));
    }

    /**
     * Permet d'ajouter une URI en DELETE
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string|null $name Nom de la route
     */
    public function delete(string $path, $callback, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute($path, $callback, ['DELETE'], $name));
    }

    /**
     * Permet d'ajouter une URI en OPTIONS
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string|null $name Nom de la route
     */
    public function options(string $path, $callback, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute($path, $callback, ['OPTIONS'], $name));
    }

    /**
     * Permet d'ajouter une URI en HEAD
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string|null $name Nom de la route
     */
    public function head(string $path, $callback, ?string $name = null)
    {
        $this->internalRouter->addRoute(new ZendRoute($path, $callback, ['HEAD'], $name));
    }

    /**
     * Permet d'ajouter une URI en GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD
     * @param string $path URI (avec paramètres ex: "/delete/{id:[0-9]+}")
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string $name Nom de la route
     */
    public function all(string $path, $callback, string $name)
    {
        $this->internalRouter->addRoute(new ZendRoute(
            $path,
            $callback,
            ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'],
            $name
        ));
    }

    /**
     * Permet d'ajouter les URIs Create, Read, Update et Delete (CRUD)
     * @param string $basePath URI de base
     * @param string|callable $callback Callback a appeler, si string, transmis au conteneur de dépendance pour résolution.
     * @param string|null $baseName Nom de base des routes
     */
    public function crud(string $basePath, $callback, string $baseName)
    {

        $this->get("$basePath", $callback, $baseName . '.index');
        $this->get("$basePath/new", $callback, $baseName . '.create');
        $this->post("$basePath/new", $callback);
        $this->get("$basePath/{id:\d+}", $callback, $baseName . '.edit');
        $this->put("$basePath/{id:\d+}", $callback);
        $this->delete("$basePath/{id:\d+}", $callback, $baseName . '.delete');
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
                $result->getMatchedMiddleware(),
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
