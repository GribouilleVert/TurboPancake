<?php
namespace TurboPancake\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Auth\Exceptions\ForbiddenException;
use TurboPancake\AuthenticationInterface;

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
        if (!$this->auth->isLogged()) {
            throw new ForbiddenException('Access denied');
        }
        return $handler->handle($request->withAttribute(UserInterface::class, $this->auth->getUser()));
    }
}
