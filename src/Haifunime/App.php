<?php
namespace Haifunime;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App {

    public function run(ServerRequestInterface $request): ResponseInterface {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) AND $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }

        $response = new Response();
        $response->getBody()->write('Salut !');
        return $response;
    }

}
