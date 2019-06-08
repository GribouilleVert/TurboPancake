<?php
namespace TurboPancake\Services\Session;

interface SessionInterface {

    /**
     * Récupère une information de session
     * @param string $key
     * @param ?mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Défini où met à jour une information de session
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
