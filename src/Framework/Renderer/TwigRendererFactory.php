<?php
namespace Framework\Renderer;

use Framework\Router\RouterTwigExtension;
use Psr\Container\ContainerInterface;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory {

    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $viewsPaths = $container->get('views.path');
        $loader = new FilesystemLoader($viewsPaths);

        if ($container->has('twig.configuration')) {
            $configuration = $container->get('twig.configuration');
        } else {
            $configuration = [];
        }

        $twig = new \Twig\Environment($loader, $configuration);
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        return new TwigRenderer($twig);
    }

}
