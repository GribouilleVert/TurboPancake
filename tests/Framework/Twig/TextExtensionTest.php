<?php
namespace Tests\Framework\Twig;

use Framework\Twig\TextExtension;
use PHPUnit\Framework\TestCase;

class TextExtensionTest extends TestCase {

    /**
     * @var TextExtension
     */
    private $textExtension;

    public function setUp(): void
    {
        $this->textExtension = new TextExtension();
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

}