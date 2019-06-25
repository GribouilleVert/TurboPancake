<?php
namespace TurboPancake\Twig;

use TurboPancake\Services\FlashService;
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
            new TwigFunction('flashs', [$this, 'getFlash'])
        ];
    }

    public function getFlash(?string $type = null): ?array
    {
        $flashes = $this->flash->get($type);

        foreach ($flashes as &$flash) {
            $flash['class'] = '';
            switch ($flash['type']) {
                case 'important':
                    $flash['class'] = 'toast-primary';
                    break;
                case 'success':
                    $flash['class'] = 'toast-success';
                    break;
                case 'warning':
                    $flash['class'] = 'toast-warning';
                    break;
                case 'error':
                    $flash['class'] = 'toast-error';
                    break;
            }
        }

        return $flashes;
    }

}
