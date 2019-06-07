<?php
namespace Framework\Services;

use Framework\Services\Session\SessionInterface;

class FlashService {

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKey = 'flashMessagesService';

    /**
     * @var
     */
    private $messagesBackup;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function info(string $message): void
    {
        $this->set('info', $message);
    }

    public function message(string $message): void
    {
        $this->set('message', $message);
    }

    public function important(string $message): void
    {
        $this->set('important', $message);
    }

    public function success(string $message): void
    {
        $this->set('success', $message);
    }

    public function warning(string $message): void
    {
        $this->set('warning', $message);
    }

    public function error(string $message): void
    {
        $this->set('error', $message);
    }

    private function set(string $type, string $message) {
        $flash = $this->session->get($this->sessionKey, []);
        $flash[$type] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function get(string $type): ?string
    {
        if (is_null($this->messagesBackup)) {
            $this->messagesBackup = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey);
        }

        if (array_key_exists($type, $this->messagesBackup)) {
            return $this->messagesBackup[$type];
        }

        return null;
    }

}