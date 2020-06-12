<?php
namespace Tests\TurboPancake\Twig;

use TurboPancake\Router\Router;
use TurboPancake\Twig\PagerFantaExtension;
use PHPUnit\Framework\TestCase;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PageFantaExtensionTest extends TestCase {

    /**
     * @var PagerFantaExtension
     */
    private $pagerFantaExtension;

    public function setUp(): void
    {
        $builder = new \DI\ContainerBuilder();
        $this->container = $builder->build();
        $this->router = $this->container->get(Router::class);
        $this->pagerFantaExtension = $this->container->get(PagerFantaExtension::class);
    }

    public function testValidExtension() {
        $this->assertInstanceOf(ExtensionInterface::class, $this->pagerFantaExtension);

        $filters = $this->pagerFantaExtension->getFilters();
        $this->assertIsArray($filters);
        $this->assertContainsOnlyInstancesOf(TwigFilter::class, $filters);

        $functions = $this->pagerFantaExtension->getFunctions();
        $this->assertIsArray($functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
    }

    //TODO:: Ecrire le test pour la fonction paginate()

}