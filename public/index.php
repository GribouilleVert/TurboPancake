<?php
require '../vendor/autoload.php';

use GuzzleHttp\Psr7\ServerRequest;
use Haifunime\App;
use function HTTP\Response\send;

$app = new App([

]);

$response = $app->run(ServerRequest::fromGlobals());

send($response);
