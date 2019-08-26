<?php
namespace TurboModule\Authentication\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Auth\Exceptions\ForbiddenException;
use TurboPancake\Router;
use TurboPancake\Router\RouterAware;
use TurboPancake\Services\Neon;
use TurboPancake\Services\Session\SessionInterface;

class ForbiddenHandlerMiddleware implements MiddlewareInterface {


    /**
     * @var Router
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $destinationRoute;

    use RouterAware;

    public function __construct(Router $router, SessionInterface $session, string $destinationRoute)
    {
        $this->router = $router;
        $this->session = $session;
        $this->destinationRoute = $destinationRoute;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ForbiddenException $e) {
            (new Neon($this->session))->warning('Vous devez être connecté pour acceder à cette page');
            $uri = $request->getUri();
            $this->session->set('auth.redirect', $uri->getPath() . '?' . $uri->getQuery());
            return $this->temporaryRedirect('auth.login');
        }
    }
}