<?php
namespace TurboPancake\Router;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TurboPancake\Exceptions\SystemException;

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
     * @throws SystemException
     */
    public function temporaryRedirect(
        string $route,
        array $parameters = [],
        array $queryParameters = []
    ): ResponseInterface {
        return $this->redirect($route, $parameters, $queryParameters, 302);
    }

    /**
     * Permet de faire une redirection définitive (301)
     * @param string $route
     * @param array $parameters
     * @param array $queryParameters
     * @return ResponseInterface
     * @throws SystemException
     */
    public function permanentRedirect(
        string $route,
        array $parameters = [],
        array $queryParameters = []
    ): ResponseInterface {
        return $this->redirect($route, $parameters, $queryParameters, 301);
    }

    /**
     * Permet de faire une générer l'objet Response de la redirection
     * @param string $route
     * @param array $parameters
     * @param array $queryParameters
     * @param int $code Code HTTP de la réponse
     * @return ResponseInterface
     * @throws SystemException
     */
    private function redirect(string $route, array $parameters, array $queryParameters, int $code): ResponseInterface
    {
        if (!isset($this->router) OR !$this->router instanceof Router) {
            throw new SystemException('Unable to use router redirect without a proper router.');
        }

        $postUri = $this->router->generateUri($route, $parameters, $queryParameters);
        return (new Response())
            ->withStatus($code)
            ->withHeader('location', $postUri);
    }
}
