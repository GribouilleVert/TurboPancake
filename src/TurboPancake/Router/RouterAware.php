<?php
namespace TurboPancake\Router;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Ajoute les méthodes liées à l'utilisation du Router
 *
 * Trait HTTPUtilitiesAction
 * @package TurboPancake\Actions
 * @property $router TurboPancake\Router
 */
trait RouterAware {

    /**
     * Permet de faire une redirection temporaire (302)
     * @param string $route
     * @param array $parameters
     * @param array $queryParameters
     * @return ResponseInterface
     */
    public function temporaryRedirect(string $route, array $parameters = [], array $queryParameters = []): ResponseInterface
    {
        return $this->redirect($route, $parameters, $queryParameters, 302);
    }

    /**
     * Permet de faire une redirection définitive (301)
     * @param string $route
     * @param array $parameters
     * @param array $queryParameters
     * @return ResponseInterface
     */
    public function permanentRedirect(string $route, array $parameters = [], array $queryParameters = []): ResponseInterface
    {
        return $this->redirect($route, $parameters, $queryParameters, 301);
    }

    /**
     * Permet de faire une générer l'objet Response de la redirection
     * @param string $route
     * @param array $parameters
     * @param array $queryParameters
     * @param int $code Code HTTP de la réponse
     * @return ResponseInterface
     */
    private function redirect(string $route, array $parameters, array $queryParameters, int $code): ResponseInterface
    {
        $postUri = $this->router->generateUri($route, $parameters, $queryParameters);
        return (new Response())
            ->withStatus($code)
            ->withHeader('location', $postUri);
    }

    /**
     * Permet de faire une redirection temporaire via une uri (302)
     * @param string $fullPath
     * @return ResponseInterface
     */
    public function directTemporaryRedirect(string $fullPath): ResponseInterface
    {
        return (new Response())
            ->withStatus(302)
            ->withHeader('location', $fullPath);
    }

}
