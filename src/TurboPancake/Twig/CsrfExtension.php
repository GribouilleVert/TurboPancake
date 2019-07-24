<?php
namespace TurboPancake\Twig;

use TurboPancake\Middleware\CsrfMiddleware;
use Twig\Extension\AbstractExtension;

final class CsrfExtension extends AbstractExtension {

    /**
     * @var CsrfMiddleware
     */
    private $csrfMiddleware;

    public function __construct(CsrfMiddleware $csrfMiddleware)
    {

        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => ['html']]),
            new \Twig\TwigFunction('csrf_token', [$this, 'csrfToken'])
        ];
    }

    public function csrfInput()
    {
        $name = $this->csrfMiddleware->getFieldName();
        $token = $this->csrfMiddleware->makeToken();
        $result =  "<input type='hidden' hidden style='display: none;' name='{$name}' value='{$token}' />";
        return $result;
    }

    public function csrfToken()
    {
        return $this->csrfMiddleware->makeToken();
    }

}
