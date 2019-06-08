<?php
namespace Tests\TurboPancake\Twig;

use TurboPancake\Twig\TimeExtension;
use PHPUnit\Framework\TestCase;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TimeExtensionTest extends TestCase {

    /**
     * @var TimeExtension
     */
    private $timeExtension;

    public function setUp(): void
    {
        $this->timeExtension = new TimeExtension();
    }
    
    public function testValidExtension() {
        $this->assertInstanceOf(ExtensionInterface::class, $this->timeExtension);

        $filters = $this->timeExtension->getFilters();
        $this->assertIsArray($filters);
        $this->assertContainsOnlyInstancesOf(TwigFilter::class, $filters);

        $functions = $this->timeExtension->getFunctions();
        $this->assertIsArray($functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
    }

    public function testDateFormatting()
    {
        $date = new \DateTime();
        $format = 'd/m/Y H:i';
        $result = '<time class="timeago" datetime="' . $date->format(\DateTime::ISO8601) . '">' . $date->format($format) . '</time>';

        $this->assertEquals($result, $this->timeExtension->ago($date, $format));
    }
}