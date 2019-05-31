<?php
namespace Tests\Framework\Twig;

use Framework\Twig\TimeExtension;
use PHPUnit\Framework\TestCase;

class TimeExtensionTest extends TestCase {

    /**
     * @var TimeExtension
     */
    private $timeExtension;

    public function setUp(): void
    {
        $this->timeExtension = new TimeExtension();
    }

    public function testDateFormatting()
    {
        $date = new \DateTime();
        $format = 'd/m/Y H:i';
        $result = '<time class="timeago" datetime="' . $date->format(\DateTime::ISO8601) . '">' . $date->format($format) . '</time>';

        $this->assertEquals($result, $this->timeExtension->ago($date, $format));
    }
}