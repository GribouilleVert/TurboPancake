<?php
namespace TurboModule\Administration\Actions;

use Psr\Container\ContainerInterface;
use TurboModule\Administration\AdminAddonInterface;
use TurboPancake\Renderer\RendererInterface;

class DashboardAction {

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

    public function __invoke()
    {
        $widgets = array_map(function (AdminAddonInterface $addon) {
            return $addon->renderWidget()??null;
        }, $this->addons);
        $widgets = array_filter($widgets, function ($widget) {
            return $widget !== null;
        });

        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }

}
