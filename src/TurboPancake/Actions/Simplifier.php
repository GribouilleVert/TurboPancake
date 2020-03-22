<?php
namespace TurboPancake\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait Simplifier {

    protected function stringResponse($page, $code = 200): ResponseInterface
    {
        return new Response($code, [], $page);
    }

    protected function jsonResponse($object, $code = 200): ResponseInterface
    {
        return new Response($code, ['Content-Type' => 'application/json'], json_encode($object));
    }

}