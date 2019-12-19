<?php
namespace TurboPancake\Twig;

use Twig\Extension\AbstractExtension;
use function TurboPancake\timeDiffText;
use function TurboPancake\ago;
use function TurboPancake\in;

final class TimeExtension extends AbstractExtension {

    public function getFilters(): array
    {
        return [
            new \Twig\TwigFilter('time_diff', [$this, 'timeDiff']),
            new \Twig\TwigFilter('ago', [$this, 'ago']),
            new \Twig\TwigFilter('in', [$this, 'in']),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('year', [$this, 'currentYear'])
        ];
    }

    public function timeDiff(\DateTime $date)
    {
        return timeDiffText($date);
    }

    public function ago(\DateTime $date)
    {
        return ago($date);
    }

    public function in(\DateTime $date)
    {
        return in($date);
    }

    public function currentYear(): string
    {
        return date('Y');
    }
}
