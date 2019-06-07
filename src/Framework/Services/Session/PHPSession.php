<?php
namespace Framework\Services\Session;

class PHPSession implements SessionInterface {

    const CREATE_SESSION_IF_ABSENT = 0;
    const IGNORE_SESSION_CREATION = 1;
    const THROW_ERROR_IF_ABSENT = 2;

    /**
     * @var int
     */
    private $comportment;

    public function __construct(int $comportment = self::CREATE_SESSION_IF_ABSENT)
    {
        $this->comportment = $comportment;
    }

    /**
     * Récupère une information de session
     * @param string $key
     * @param ?mixed $default
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key, $default = null)
    {
        $this->ensureStarted();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * Défini où met à jour une information de session
     * @param string $key
     * @param mixed $value
     * @throws \Exception
     */
    public function set(string $key, $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Supprime une information de session
     * @param string $key
     * @throws \Exception
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * S'assure que la session est démarrée et si ce n'est pas le cas, suis la directive $this->comportment
     * @throws \Exception
     */
    private function ensureStarted()
    {
        $status = session_status();
        if ($status === PHP_SESSION_NONE) {
            if ($this->comportment === self::CREATE_SESSION_IF_ABSENT) {
                session_start();
            } elseif ($this->comportment === self::THROW_ERROR_IF_ABSENT) {
                throw new \Exception('PHP Session is not started', E_ERROR);
            } elseif ($this->comportment === self::IGNORE_SESSION_CREATION) {
                null;
            } else {
                throw new \Exception('Invalid comportment', E_ERROR);
            }
        } elseif ($status === PHP_SESSION_DISABLED) {
            throw new \Exception('PHP Session is disabled', E_ERROR);
        }
    }

}
