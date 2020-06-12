<?php
namespace TurboModule\Administration;

use TurboModule\Administration\Actions\DashboardAction;
use TurboPancake\Module;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Renderer\TwigRenderer;
use TurboPancake\Router\Router;

final class AdministrationModule extends Module {

    /**
     * Configuration du conteneur de dépendances
     */
    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var AdminTwigExtension
     */
    private $twigExtension;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        string $prefix,
        AdminTwigExtension $twigExtension
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->prefix = $prefix;
        $this->twigExtension = $twigExtension;
    }

    public function load(): void
    {
        $this->renderer->addPath(__DIR__ . '/views', 'admin');

        if ($this->renderer instanceof TwigRenderer) {
            $this->renderer->getTwig()->addExtension($this->twigExtension);
        }

        $this->router->get($this->prefix, DashboardAction::class, 'admin.dashboard');
    }

    public function getModuleDependencies(): array
    {
        return [
            \TurboModule\Authentication\AuthenticationModule::class,
        ];
    }
}
