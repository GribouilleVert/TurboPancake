<?php
namespace TurboModule\Authentication;

use Psr\Container\ContainerInterface;
use TurboModule\Authentication\Actions\AttemptLoginAction;
use TurboModule\Authentication\Actions\LoginAction;
use TurboModule\Authentication\Actions\LogoutAction;
use TurboPancake\Module;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;

class AuthenticationModule extends Module {

    /**
     * Configuration du conteneur de dépendances
     */
    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * Dossiers pour la gestion de la base de donnée
     */
    const MIGRATIONS = __DIR__ . '/Database/mgmt/migrations';
    const SEEDS = __DIR__ . '/Database/mgmt/seeds';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load(): void
    {
        $this->container->get(RendererInterface::class)->addPath(__DIR__ . '/views', 'auth');


        $router = $this->container->get(Router::class);

        $router->get($this->container->get('auth.login'), LoginAction::class, 'auth.login');
        $router->post($this->container->get('auth.login'), AttemptLoginAction::class);
        $router->delete($this->container->get('auth.logout'), LogoutAction::class, 'auth.logout');
    }

    public function getMiddlewareDependencies(): array
    {
        return [
            \TurboModule\Authentication\Middlewares\AuthCheckerMiddleware::class,
            \TurboModule\Authentication\Middlewares\ForbiddenHandlerMiddleware::class,
        ];
    }
}
