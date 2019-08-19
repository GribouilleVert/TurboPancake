<?php
namespace TurboPancake\Middlewares;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Middlewares\Exceptions\CsrfException;

class CsrfMiddleware implements MiddlewareInterface {

    private $fieldName = '_csrf';

    private $sessionKey = 'csrf';

    private $tokenLimit = 15;

    /**
     * @var \ArrayAccess|array
     * @throws \TypeError
     */
    private $session;

    public function __construct(
        &$session = [],
        int $tokenLimit = 15,
        string $fieldName = '_csrf',
        string $sessionKey = 'csrf'
    ) {
        $this->isValidSession($session);
        $this->session = &$session;
        $this->session = &$session;
        $this->fieldName = $fieldName;
        $this->sessionKey = $sessionKey;
        $this->tokenLimit = $tokenLimit;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $params = $request->getParsedBody();
            if (is_null($params)) {
                return $delegate->process($request);
            } elseif (!array_key_exists($this->fieldName, $params)) {
                return $this->reject();
            } else {
                $tokensList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->fieldName], $tokensList)) {
                    $this->useToken($params[$this->fieldName]);
                    return $delegate->process($request);
                } else {
                    return $this->reject();
                }
            }
        }
        return $delegate->process($request);
    }

    /**
     * Génère un token aléatoire cryptographiquement sécurisé
     * @return string
     * @throws \Exception
     */
    public function makeToken(): string
    {
        $token = bin2hex(random_bytes(16));

        $tokensList = $this->session[$this->sessionKey] ?? [];
        $tokensList[] = $token;
        $this->session[$this->sessionKey] = $tokensList;

        $this->limitTokens();
        return $token;
    }

    /**
     * @param $token
     */
    private function useToken($token): void
    {
        $tokensList = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $t !== $token;
        });
        $this->session[$this->sessionKey] = $tokensList;
    }

    private function limitTokens(): void
    {
        $tokensList = $this->session[$this->sessionKey] ?? [];
        if (count($tokensList) > $this->tokenLimit) {
            array_shift($tokensList);
            $this->session[$this->sessionKey] = $tokensList;
        }
    }

    /**
     * Actions a faire en cas de token invalide
     * @throws \Exception
     * @return ResponseInterface
     */
    private function reject(): ResponseInterface
    {
        throw new CsrfException('Unable to verify user request\'s authenticity.');
    }

    /**
     * @param $session array|\ArrayAccess
     * @throws \TypeError
     */
    private function isValidSession($session): void
    {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError(
                '$session must be an array or an \ArrayAccess instance, got ' . gettype($session) . ' .'
            );
        }
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

}
