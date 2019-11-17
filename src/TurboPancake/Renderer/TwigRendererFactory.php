<?php
namespace TurboPancake\Renderer;

use TurboPancake\Router\RouterTwigExtension;
use Psr\Container\ContainerInterface;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory {

    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $production = $container->get('env') === 'production';
        $viewsPaths = $container->get('views.path');
        $loader = new FilesystemLoader($viewsPaths);

        if ($container->has('twig.configuration')) {
            $configuration = $container->get('twig.configuration');
        } else {
            $configuration = [];
        }

        $configuration = array_merge($configuration, [
            'debug' => !$production,
            'auto_reload' => !$production,
            'cache' => $production ? 'tmp/twig' : false,
        ]);

        $twig = new \Twig\Environment($loader, $configuration);
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        return new TwigRenderer($twig);
    }
}
