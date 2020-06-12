<?php
namespace TurboModule\Authentication\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Auth\Exceptions\ForbiddenException;
use TurboPancake\Auth\Exceptions\NotLoggedException;
use TurboPancake\Auth\AuthenticationInterface;

class AuthCheckerMiddleware implements MiddlewareInterface {

    /**
     * @var AuthenticationInterface
     */
    private $auth;

    public function __construct(AuthenticationInterface $auth)
    {
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $currentPath = $request->getUri()->getPath();
        if (strpos($currentPath, '/cockpit') === 0) {
            if (!$this->auth->isLogged()) {
                throw new NotLoggedException('Access denied');
            }
            $request = $request->withAttribute(UserInterface::class, $this->auth->getUser());
        }
        return $handler->handle($request);
    }
}
