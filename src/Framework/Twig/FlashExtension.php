<?php
namespace Framework\Twig;

use Framework\Services\FlashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FlashExtension extends AbstractExtension {

    /**
     * @var FlashService
     */
    private $flash;

    public function __construct(FlashService $flash)
    {
        $this->flash = $flash;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [$this, 'getFlash'])
        ];
    }

    public function getFlash(string $type): ?string
    {
        return $this->flash->get($type);
    }

}