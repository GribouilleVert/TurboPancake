<?php
namespace Haifunime\Administration;

use Framework\Module;
use Framework\Renderer\RendererInterface;

class AdministrationModule extends Module {

    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'admin');
    }

}
