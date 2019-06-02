<?php
namespace Tests\Framework\Twig;

use Framework\Router;
use Framework\Router\RouterTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RouterExtensionTest extends TestCase {

    /**
     * @var RouterTwigExtension
     */
    private $routerExtension;

    /**
     * @var \DI\Container
     */
    private $container;

    /**
     * @var Router
     */
    private $router;

    public function setUp(): void
    {
        $builder = new \DI\ContainerBuilder();
        $this->container = $builder->build();
        $this->router = $this->container->get(Router::class);
        $this->routerExtension = $this->container->get(RouterTwigExtension::class);
    }

    public function testValidExtension() {
        $this->assertInstanceOf(ExtensionInterface::class, $this->routerExtension);

        $filters = $this->routerExtension->getFilters();
        $this->assertIsArray($filters);
        $this->assertContainsOnlyInstancesOf(TwigFilter::class, $filters);

        $functions = $this->routerExtension->getFunctions();
        $this->assertIsArray($functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
    }

    public function testPathGeneration() {
        $this->router->get('/test', function(){}, 'test');

        $uri = $this->routerExtension->generatePath('test');
        $this->assertEquals('/test', $uri);
    }

    public function testPathGenerationWithParameters() {
        $this->router->get('/test/{text}', function(){}, 'test');

        $uri = $this->routerExtension->generatePath('test', ['text' => 'abc']);
        $this->assertEquals('/test/abc', $uri);
    }

    public function testPathGenerationWithGetParameters() {
        $this->router->get('/test', function(){}, 'test');

        $uri = $this->routerExtension->generatePath('test', [], ['text' => 'abc']);
        $this->assertEquals('/test?text=abc', $uri);
    }

}