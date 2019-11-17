<?php
namespace TurboPancake\Router;

use Psr\Container\ContainerInterface;
use TurboPancake\Router;

class RouterFactory {

    public function __invoke(ContainerInterface $container)
    {
        $cache = null;
        if ($container->get('env') === 'production') {
            $cache = 'tmp/routes.php';
        }
        return new Router($container, $cache);
    }
}
