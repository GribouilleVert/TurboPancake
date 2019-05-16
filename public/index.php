<?php
require_once '../vendor/autoload.php';

use GuzzleHttp\Psr7\ServerRequest;
use Framework\App;
use function HTTP\Response\send;

$app = new App([
    \Haifunime\Blog\BlogModule::class
]);

$response = $app->run(ServerRequest::fromGlobals());

send($response);
