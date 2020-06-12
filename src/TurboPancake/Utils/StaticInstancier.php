<?php
namespace TurboPancake\Utils;

use Psr\Container\ContainerInterface;

class StaticInstancier {

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $classList = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function initClass(string $className): void
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('The class name doesn\'t corresponsd to any known class.');
        }

        $implementations = class_implements($className);
        if (!in_array(StaticInstanciedInterface::class, $implementations)) {
            throw new \InvalidArgumentException('The class name doesn\'t correspond to a class wich implements ' . StaticInstanciedInterface::class . '.');
        }

        if (in_array($className, $this->classList)) {
            return; //Déjà instanciée
        }

        call_user_func([$className, 'init'], $this->container);
        $this->classList[] = $className;
    }
}