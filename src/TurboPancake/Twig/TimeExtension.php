<?php
namespace TurboPancake\Twig;

use Twig\Extension\AbstractExtension;

final class TimeExtension extends AbstractExtension {

    public function getFilters(): array
    {
        return [
            new \Twig\TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']]),
        ];
    }

    public function ago(\DateTime $date, string $format = 'd/m/Y H:i')
    {
        return  $result =
            '<time class="timeago" datetime="' . $date->format(\DateTime::ISO8601) . '">'
            . $date->format($format) .
            '</time>';
    }

}
