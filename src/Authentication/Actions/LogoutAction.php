<?php
namespace TurboModule\Authentication\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Router\RouterAware;
use TurboPancake\AuthenticationInterface;
use TurboPancake\Router;
use TurboPancake\Services\Neon;

class LogoutAction implements MiddlewareInterface {

    /**
     * @var AuthenticationInterface
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
        AuthenticationInterface $authentification,
        Router $router,
        Neon $flash,
        string $afterLogoutRoute
    ) {

        $this->authentification = $authentification;
        $this->router = $router;
        $this->flash = $flash;
        $this->afterLogoutRoute = $afterLogoutRoute;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->authentification->logout();
        $this->flash->important('Vous avez été déconnecté');
        return $this->temporaryRedirect($this->afterLogoutRoute);
    }
}
