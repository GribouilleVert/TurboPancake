<?php
namespace TurboModule\Administration;

use TurboPancake\Module;
use TurboPancake\Renderer\RendererInterface;

class AdministrationModule extends Module {

    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'admin');
    }

}
