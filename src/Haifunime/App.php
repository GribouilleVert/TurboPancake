<?php
namespace Haifunime;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class App
 * @package Haifunime
 */
class App {

    /**
     * @var array
     */
    private $module = [];

    /**
     * App constructor.
     * @param array $modules Liste des modules a charger
     */
    public function __construct(array $modules = [])
    {
        foreach ($modules as $module)
            $this->module[] = new $module();
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
    }

}
