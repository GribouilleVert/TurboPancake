<?php
namespace Tests\TurboPancake\Twig;

use TurboPancake\Twig\TextExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TextExtensionTest extends TestCase {

    /**
     * @var TextExtension
     */
    private $textExtension;

    public function setUp(): void
    {
        $this->textExtension = new TextExtension();
    }

    public function testValidExtension() {
        $this->assertInstanceOf(ExtensionInterface::class, $this->textExtension);

        $filters = $this->textExtension->getFilters();
        $this->assertIsArray($filters);
        $this->assertContainsOnlyInstancesOf(TwigFilter::class, $filters);

        $functions = $this->textExtension->getFunctions();
        $this->assertIsArray($functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
    }

    public function testTooShortTest() {
        $text = 'J\'aime les trains.';
        $excerpt = $this->textExtension->excerpt($text);

        $this->assertEquals($text, $excerpt);
    }

    public function testLongTextNoWordCut() {
        $text = 'J\'aime les trains.';
        $excerpt = $this->textExtension->excerpt($text, 7);

        $this->assertEquals('J\'aime...', $excerpt);
    }

    public function testDump() {
        $text = 'dump';

        $dump = $this->textExtension->dump($text);

        $cloner = new VarCloner();
        $dumper = new CliDumper();
        $expected = $dumper->dump($cloner->cloneVar($text), true);

        $this->assertEquals($expected, $dump);
    }

}