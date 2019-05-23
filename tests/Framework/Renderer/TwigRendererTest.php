<?php
namespace Tests\Framework;

use Framework\Renderer\TwigRenderer;
use PHPUnit\Framework\TestCase;

class TwigRendererTest extends TestCase {

    /**
     * @var TwigRenderer
     */
    private $renderer;

    public function testCorrectPathRender() {
        $this->renderer = new TwigRenderer(__DIR__ . '/views');

        $this->renderer->addPath(__DIR__ . '/views', 'blog');
        $content = $this->renderer->render('@blog/demo');

        $this->assertEquals(file_get_contents(__DIR__ . '/views/demo.twig'), $content);
    }

    public function testConstructorPathRender() {
        $this->renderer = new TwigRenderer(__DIR__ . '/views');

        $content = $this->renderer->render('demo');

        $this->assertEquals(file_get_contents(__DIR__ . '/views/demo.php'), $content);
    }

    public function testRenderWithParams() {
        $this->renderer = new TwigRenderer(__DIR__ . '/views');

        $content = $this->renderer->render('hello', ['name' => 'Vasco']);

        $this->assertEquals('Salut Vasco !', $content);
    }

    public function testRenderWithGlobalParams() {
        $this->renderer = new TwigRenderer(__DIR__ . '/views');

        $this->renderer->addGlobal('name', 'Vasco');
        $content = $this->renderer->render('hello');

        $this->assertEquals('Salut Vasco !', $content);
    }


}