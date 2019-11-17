<?php
namespace TurboModule\Authentication;

use TurboPancake\AuthentificationInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthTwigExtension extends AbstractExtension {

    /**
     * @var AuthentificationInterface
     */
    private $authentification;

    public function __construct(AuthentificationInterface $authentification)
    {
        $this->authentification = $authentification;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_logged', [$this->authentification, 'isLogged']),
            new TwigFunction('get_current_user', [$this->authentification, 'getUser']),
        ];
    }
}
