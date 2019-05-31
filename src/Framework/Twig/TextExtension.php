<?php
namespace Framework\Twig;

use Twig\Extension\AbstractExtension;

class TextExtension extends AbstractExtension {

    public function getFilters(): array
    {
        return [
            new \Twig\TwigFilter('excerpt', [$this, 'excerpt']),
        ];
    }

    public function excerpt($content, $maxlength = 100) {
        if (mb_strlen($content) > $maxlength) {
            $excerpt = mb_substr($content, 0, $maxlength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }

}