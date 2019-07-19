<?php
namespace Tests\TurboPancake\Renderer;

use Twig\Extension\AbstractExtension;

final class TwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction('upper', [$this, 'toUpperCase']),
        ];
    }

    public function toUpperCase(string $text): string
    {
        return strtoupper($text);
    }

}