<?php
namespace TurboPancake\Actions;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait Simplifier {

    /**
     * Crée une réponse à partir d'une chaine de caractères
     *
     * @param string $text Le texte à envoyer
     * @param int $code
     * @return ResponseInterface
     */
    protected function stringResponse(string $text, int $code = 200): ResponseInterface
    {
        return new Response($code, [], $text);
    }

    /**
     * Crée une réponse json à partir d'un objet
     *
     * @param mixed $object
     * @param int $code
     * @return ResponseInterface
     */
    protected function jsonResponse($object, int $code = 200): ResponseInterface
    {
        return new Response($code, ['Content-Type' => 'application/json'], json_encode($object));
    }

    /**
     * Crée une réponse xml à partir d'une chaine de caractères
     *
     * @param string $xml Le xml a envoyer
     * @param int $code
     * @return ResponseInterface
     */
    protected function xmlResponse(string $xml, int $code = 200): ResponseInterface
    {
        return new Response($code, ['Content-Type' => 'application/xml'], $xml);
    }

    /**
     * Permet de faire une redirection temporaire via une uri (302)
     * @param string $fullPath
     * @return ResponseInterface
     */
    public function directRedirect(string $fullPath): ResponseInterface
    {
        return (new Response())
            ->withStatus(302)
            ->withHeader('location', $fullPath);
    }

}