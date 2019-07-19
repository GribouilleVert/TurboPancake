<?php
namespace TurboPancake\Container;

use Psr\Container\ContainerInterface;

class ContainerFactory {

    /**
     * Permet de construire le conteneur d'inkjection de dépendances
     *
     * @param array $modules
     * @return \DI\Container
     * @throws \Exception
     */
    public function __invoke(array $modules): ContainerInterface
    {
        $builder = new \DI\ContainerBuilder();
        $builder->addDefinitions(ROOT . '/config/config.php');
        foreach ($modules as $module) {
            if (!is_null($module::DEFINITIONS)) {
                $builder->addDefinitions($module::DEFINITIONS);
            }
        }
        $builder->addDefinitions(ROOT . '/config.php');
        return $builder->build();
    }

}
