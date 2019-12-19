<?php
namespace TurboPancake\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait Simplifier {

    protected function returnString($page, $code = 200): ResponseInterface
    {
        return new Response($code, [], $page);
    }

}