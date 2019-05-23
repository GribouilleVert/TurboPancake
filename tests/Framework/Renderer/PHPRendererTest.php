<?php
namespace Tests\Framework;

use Framework\Renderer;
use Framework\Renderer\PHPRenderer;
use PHPUnit\Framework\TestCase;

class PHPRendererTest extends TestCase {

    /**
     * @var PHPRenderer
     */
    private $renderer;

    public function setUp(): void
    {
        $this->renderer = new PHPRenderer();
        $this->renderer->addPath(__DIR__ . '/views');
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