<?php
namespace Framework\Services\Session;

/**
 * Class ArraySession
 * @package Framework\Services\Session
 *
 * @warning NE PAS UTILISER EN PRODUCTION
 */
final class ArraySession implements SessionInterface {

    /**
     * @var array Session Data Saver
     */
    private $data = [];

    /**
     * Récupère une information de session
     * @param string $key
     * @param ?mixed $default
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
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
        $this->data[$key] = $value;
    }

    /**
     * Supprime une information de session
     * @param string $key
     * @throws \Exception
     */
    public function delete(string $key): void
    {
        unset($this->data[$key]);
    }

}
