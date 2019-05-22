<?php

require_once '../vendor/autoload.php';

$renderer = new \Framework\Renderer();
$renderer->addPath(dirname(__DIR__) . '/views');

$app = new Framework\App([
    \Haifunime\Blog\BlogModule::class
], [
    'renderer'  => $renderer
]);

$response = $app->run(GuzzleHttp\Psr7\ServerRequest::fromGlobals());

\Http\Response\send($response);
