<?php
namespace TurboPancake\Services;

use TurboPancake\Services\Flash\FlashRendererInterface;
use TurboPancake\Services\Session\SessionInterface;

class Flash {

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

    private function set(string $type, string $message)
    {
        $flash = $this->session->get($this->sessionKey, []);
        if (!isset($flash[$type])) {
            $flash[$type] = [];
        }
        $flash[$type][] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function get(?string $type = null, bool $keep = false): ?array
    {
        if (is_null($this->messagesBackup)) {
            $this->messagesBackup = $this->session->get($this->sessionKey, []);
            if (!$keep) {
                $this->session->delete($this->sessionKey);
            }
        }

        $result = [];
        if (is_null($type)) {
            foreach ($this->messagesBackup as $type => $messageArray) {
                foreach ($messageArray as $message) {
                    $result[] = [
                        'type' => $type,
                        'message' => $message
                    ];
                }
            }
            return $result;
        } elseif (array_key_exists($type, $this->messagesBackup)) {
            foreach ($this->messagesBackup[$type] as $message) {
                $result[] = [
                    'type' => $type,
                    'message' => $message
                ];
            }
            return $result;
        }

        return null;
    }

}
