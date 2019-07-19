<?php
namespace TurboModule\Administration;

use TurboModule\Administration\Actions\DashboardAction;
use TurboPancake\Module;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Renderer\TwigRenderer;
use TurboPancake\Router;

final class AdministrationModule extends Module {

    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        string $prefix,
        AdminTwigExtension $twigExtension
    ) {
        $renderer->addPath(__DIR__ . '/views', 'admin');

        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($twigExtension);
        }

        $router->get($prefix, DashboardAction::class, 'admin.dashboard');
    }

}
