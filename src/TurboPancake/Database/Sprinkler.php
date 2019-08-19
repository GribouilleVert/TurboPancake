<?php
namespace TurboPancake\Database;

class Sprinkler {

    public static function hydrate(array $datas, string $object): object
    {
        $instance = new $object();
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

}
