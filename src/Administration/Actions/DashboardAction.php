<?php
namespace TurboModule\Administration\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboModule\Administration\AdminAddonInterface;
use TurboPancake\Renderer\RendererInterface;
use Psr\Http\Message\ResponseInterface;

class DashboardAction implements MiddlewareInterface {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var AdminAddonInterface[]
     */
    private $addons;

    public function __construct(RendererInterface $renderer, array $addons)
    {

        $this->renderer = $renderer;
        $this->addons = $addons;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $widgets = array_map(function (AdminAddonInterface $addon) {
            return $addon->renderWidget()??null;
        }, $this->addons);
        $widgets = array_filter($widgets, function ($widget) {
            return $widget !== null;
        });

        return new Response(200, [], $this->renderer->render(
            '@admin/dashboard',
            compact('widgets')
        ));
    }
}
