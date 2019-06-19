<?php
namespace Tests\TurboPancake\Twig;

use PHPUnit\Framework\TestCase;
use TurboPancake\Twig\FormExtension;
use TurboPancake\Validator\ValidationError;

class FormExtensionTest extends TestCase {

    /**
     * @var FormExtension
     */
    private $formExtension;

    public function setUp(): void
    {
        $this->formExtension = new FormExtension();
    }

    private function trim(string $string): string
    {
        $lines = explode(PHP_EOL, $string);
        $lines = array_map('trim', $lines);
        return join('', $lines);
    }

    public function assertSimilar(string $expected, string $actual): void
    {
        $this->assertEquals(
            $this->trim($expected),
            $this->trim($actual)
        );
    }

    public function testInput()
    {
        $html = $this->formExtension->field([], 'demo', 'Demo', 'This is a demo');
        $this->assertSimilar("
        <div class=\"form-group\">
            <label class=\"form-label\" for=\"demo\">Demo</label>
            <input class=\"form-input\" id=\"demo\" name=\"demo\" type=\"text\" value=\"This is a demo\">
        </div>
        ", $html);
    }

    public function testInputWithError()
    {
        $context = [
            'errors' => [
                'demo' => new ValidationError('demo', 'empty')
            ]
        ];
        $html = $this->formExtension->field($context, 'demo', 'Demo', 'This is a demo');
        $this->assertSimilar("
        <div class=\"form-group has-error\">
            <label class=\"form-label\" for=\"demo\">Demo</label>
            <input class=\"form-input\" id=\"demo\" name=\"demo\" type=\"text\" value=\"This is a demo\">
            <p class=\"form-input-hint\">Le champ demo ne doit pas être vide.</p>
        </div>
        ", $html);
    }

    public function testTextArea()
    {
        $html = $this->formExtension->field([], 'demo', 'Demo', 'This is a demo', ['type' => 'textarea']);
        $this->assertSimilar("
        <div class=\"form-group\">
            <label class=\"form-label\" for=\"demo\">Demo</label>
            <textarea class=\"form-input\" id=\"demo\" name=\"demo\">This is a demo</textarea>
        </div>
        ", $html);
    }

}