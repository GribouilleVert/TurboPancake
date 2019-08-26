<?php
namespace TurboModule\Authentication\Actions;

use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Renderer\RendererInterface;

class LoginAction {

    /**
     * @var RendererInterface
     */
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->renderer->render('@auth/login');
    }

}