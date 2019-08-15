<?php
namespace Tests\TurboPancake;

use DI\Container;
use TurboPancake\Renderer\TwigRendererFactory;
use TurboPancake\Router;
use PHPUnit\Framework\TestCase;
use Tests\TurboPancake\Renderer\TwigExtension;
use Twig\Error\RuntimeError;

class TwigRendererTest extends TestCase {

    /**
     * @var TwigRendererFactory
     */
    private $renderer;

    public function setUp(): void {
        $container = new Container();
        $container->set('views.path', __DIR__ . '/views');
        $container->set('env', getenv('ENV'));
        $this->renderer = (new TwigRendererFactory)->__invoke($container);
    }

    public function testCorrectPathRender() {
        $this->renderer->addPath(__DIR__ . '/views', 'blog');
        $content = $this->renderer->render('@blog/demo');

        $this->assertEquals(file_get_contents(__DIR__ . '/views/demo.twig'), $content);
    }

    public function testConstructorPathRender() {
        $content = $this->renderer->render('demo');

        $this->assertEquals(file_get_contents(__DIR__ . '/views/demo.twig'), $content);
    }

    public function testRenderWithParams() {
        $content = $this->renderer->render('hello', ['name' => 'Vasco']);

        $this->assertEquals('Salut Vasco !', $content);
    }

    public function testRenderWithGlobalParams() {
        $this->renderer->addGlobal('name', 'Vasco');
        $content = $this->renderer->render('hello');

        $this->assertEquals('Salut Vasco !', $content);
    }

    public function testRendererWithCustomConfiguration() {
        $container = new Container();
        $container->set('views.path', __DIR__ . '/views');
        $container->set('env', getenv('ENV'));
        $container->set('twig.configuration', [
            'strict_variables' => true
        ]);
        $this->renderer = (new TwigRendererFactory)->__invoke($container);

        $this->expectException(RuntimeError::class);
        $this->renderer->render('invalid_variable');
    }

    public function testRendererWithCustomExtension() {
        $container = new Container();
        $container->set('views.path', __DIR__ . '/views');
        $container->set('env', getenv('ENV'));
        $container->set('twig.extensions', [
            new TwigExtension()
        ]);
        $this->renderer = (new TwigRendererFactory)->__invoke($container);
        $content = $this->renderer->render('test_extension');

        $this->assertEquals('ABCDEF', $content);
    }
    
}