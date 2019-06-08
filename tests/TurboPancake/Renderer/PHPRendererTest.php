<?php
namespace Tests\TurboPancake;

use DI\Container;
use TurboPancake\Renderer\PHPRenderer;
use TurboPancake\Renderer\PHPRendererFactory;
use TurboPancake\Renderer\TwigRendererFactory;
use PHPUnit\Framework\TestCase;

class PHPRendererTest extends TestCase {

    /**
     * @var PHPRenderer
     */
    private $renderer;

    public function setUp(): void
    {
        $container = new Container();
        $container->set('views.path', __DIR__ . '/views');
        $this->renderer = (new PHPRendererFactory())->__invoke($container);
    }

    public function testCorrectPathRender() {
        $this->renderer->addPath(__DIR__ . '/views', 'blog');
        $content = $this->renderer->render('@blog/demo');

        $this->assertEquals(file_get_contents(__DIR__ . '/views/demo.php'), $content);
    }

    public function testDefaultPathRender() {
        $content = $this->renderer->render('demo');

        $this->assertEquals(file_get_contents(__DIR__ . '/views/demo.php'), $content);
    }

    public function testConstructorPathRender() {
        $this->renderer = new PHPRenderer(__DIR__ . '/views');

        $content = $this->renderer->render('demo');

        $this->assertEquals(file_get_contents(__DIR__ . '/views/demo.php'), $content);
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


}