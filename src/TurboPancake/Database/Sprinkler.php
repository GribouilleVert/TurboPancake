<?php
namespace TurboPancake\Database;

use Psr\Container\ContainerInterface;

class Sprinkler {

    /**
     * @var ContainerInterface
     */
    private static $container;

    public static function hydrate(array $datas, $object): object
    {
        if (is_string($object)) {
            $instance = self::$container->make($object);
        } elseif (is_object($object)) {
            $instance = $object;
        } else {
            throw new \TypeError('Unexpected type ' . gettype($object) . ' expected string or object');
        }

        foreach ($datas as $key => $value) {
            $method = self::getSetter($key);
            if (method_exists($object, $method)) {
                $instance->$method($value);
            } else {
                $property = self::getProperty($key);
                $instance->$property = $value;
            }
        }
        return $instance;
    }

    private static function getSetter(string $name): string
    {
        preg_match_all('/_([a-z])/', $name, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $name = str_replace($match[0], strtoupper($match[1]), $name);
        }
        return 'set' . ucfirst($name);
    }

    private static function getProperty(string $name): string
    {
        preg_match_all('/_([a-z])/', $name, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $name = str_replace($match[0], strtoupper($match[1]), $name);
        }
        return $name;
    }

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }
}
