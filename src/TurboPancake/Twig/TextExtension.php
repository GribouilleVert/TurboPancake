<?php
namespace TurboPancake\Twig;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Twig\Extension\AbstractExtension;

final class TextExtension extends AbstractExtension {

    public function getFilters(): array
    {
        return [
            new \Twig\TwigFilter('excerpt', [$this, 'excerpt']),
            new \Twig\TwigFilter('dump', [$this, 'dump'], ['is_safe' => ['html']]),
        ];
    }

    public function excerpt($content, $maxlength = 100)
    {
        if (mb_strlen($content) > $maxlength) {
            $excerpt = mb_substr($content, 0, $maxlength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }

    public function dump($variable, ?string $theme = null): string
    {
        $cloner = new VarCloner();
        if (php_sapi_name() === 'cli') {
            $dumper = new CliDumper();
        } else {
            if (is_null($theme) OR !in_array($theme, ['dark', 'light'])) {
                $theme = 'light';
            }
            $dumper = new HtmlDumper();
            $dumper->setTheme($theme);
        }
        return $dumper->dump($cloner->cloneVar($variable), true);
    }

}
