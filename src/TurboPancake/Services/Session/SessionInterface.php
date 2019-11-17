<?php
namespace TurboPancake\Services\Session;

interface SessionInterface extends \ArrayAccess {

    /**
     * Récupère une information de session
     * @param string $key
     * @param ?mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Défini ou met à jour une information de session
     * @param string $key
     * @param mixed$value
     */
    public function set(string $key, $value): void;

    /**
     * Supprime une information de session
     * @param string $key
     */
    public function delete(string $key): void;
}
