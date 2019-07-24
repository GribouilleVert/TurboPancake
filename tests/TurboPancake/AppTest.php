<?php
namespace Tests\TurboPancake;

use Psr\Container\ContainerInterface;
use TurboPancake\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase {

    public function testGetContainer() {
        $app = new App([], []);
        $container = $app->getContainer();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

}