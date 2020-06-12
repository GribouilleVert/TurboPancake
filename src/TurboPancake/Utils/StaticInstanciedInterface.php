<?php
namespace TurboPancake\Utils;

use Psr\Container\ContainerInterface;

interface StaticInstanciedInterface {
    
    public static function init(ContainerInterface $container): void;
    
}