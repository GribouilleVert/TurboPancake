<?php
namespace TurboModule\Administration;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AdminTwigExtension extends AbstractExtension {
    /**
     * @var array|AdminAddonInterface[]
     */
    private $addons;

    /**
     * AdminTwigExtension constructor.
     * @param AdminAddonInterface[] $addons
     */
    public function __construct(array $addons)
    {
        $this->addons = $addons;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
        ];
    }

    public function renderMenu(): string
    {
        return array_reduce($this->addons, function (string $html, AdminAddonInterface $addon) {
            $menuItem = $addon->renderMenuItem();
            if ($menuItem !== null) {
                $html .= $menuItem;
            }
            return $html;
        }, '');
    }

}
