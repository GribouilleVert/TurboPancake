<?php
namespace TurboPancake\Renderer;

use Psr\Container\ContainerInterface;

final class PHPRendererFactory {

    public function __invoke(ContainerInterface $container): PHPRenderer
    {
        return new PHPRenderer($container->get('views.path'));
    }
}
