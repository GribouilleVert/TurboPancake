<?php
namespace TurboModule\Authentication;

use TurboPancake\Auth\AuthenticationInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthTwigExtension extends AbstractExtension {

    /**
     * @var AuthenticationInterface
     */
    private $authentification;

    public function __construct(AuthenticationInterface $authentification)
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
