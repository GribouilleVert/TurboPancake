<?php
namespace TurboModule\Authentication\Actions;

use TurboPancake\Router\RouterAware;
use TurboPancake\AuthentificationInterface;
use TurboPancake\Router;
use TurboPancake\Services\Neon;

class LogoutAction {

    /**
     * @var AuthentificationInterface
     */
    private $authentification;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Neon
     */
    private $flash;

    /**
     * @var string
     */
    private $afterLogoutRoute;

    use RouterAware;

    public function __construct(
        AuthentificationInterface $authentification,
        Router $router,
        Neon $flash,
        string $afterLogoutRoute
    ) {

        $this->authentification = $authentification;
        $this->router = $router;
        $this->flash = $flash;
        $this->afterLogoutRoute = $afterLogoutRoute;
    }

    public function __invoke()
    {
        $this->authentification->logout();
        $this->flash->important('Vous avez été déconnecté');
        return $this->temporaryRedirect($this->afterLogoutRoute);
    }

}
