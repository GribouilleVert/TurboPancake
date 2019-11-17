<?php
namespace TurboPancake\Services\Session;

/**
 * Class ArraySession
 * @package TurboPancake\Services\Session
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
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Supprime une information de session
     * @param string $key
     */
    public function delete(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * Si un offset existe
     * @param mixed $offset <p>
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @alias $this->get
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @alias $this->set
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @alias $this->delete
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }
}
