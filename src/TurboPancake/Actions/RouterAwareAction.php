<?php
namespace TurboPancake\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Ajoute les méthodes liées à l'utilisation du Router
 *
 * Trait HTTPUtilitiesAction
 * @package TurboPancake\Actions
 * @property $router TurboPancake\Router
 */
trait RouterAwareAction {

    /**
     * Permet de faire une redirection temporaire (302)
     * @param string $route
     * @param array $parameters
     * @return ResponseInterface
     */
    public function temporaryRedirect(string $route, array $parameters = []): ResponseInterface
    {
        return $this->redirect($route, $parameters, 302);
    }

    /**
     * Permet de faire une redirection définitive (301)
     * @param string $route
     * @param array $parameters
     * @return ResponseInterface
     */
    public function permanentRedirect(string $route, array $parameters = []): ResponseInterface
    {
        return $this->redirect($route, $parameters, 301);
    }

    /**
     * Permet de faire une générer l'objet Response de la redirection
     * @param string $route
     * @param array $parameters
     * @param int $code Code HTTP de la réponse
     * @return ResponseInterface
     */
    private function redirect(string $route, array $parameters, int $code): ResponseInterface
    {
        $postUri = $this->router->generateUri($route, $parameters);
        return (new Response())
            ->withStatus($code)
            ->withHeader('location', $postUri);
    }

}
