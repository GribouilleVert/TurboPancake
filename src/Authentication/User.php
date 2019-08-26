<?php
namespace TurboModule\Authentication;

use TurboPancake\Auth\UserInterface;

class User implements UserInterface {

    /**
     * @var string
     */
    public $id;

    /**
     * @var string Nom de l'utilisateur
     */
    public $username;

    /**
     * @return string L'identifiant de l'utilisateur sous forme de chaine de caractère
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string Le nom d'utilisateur
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string[] La liste des roles d'un utilisateur
     */
    public function getRoles(): array
    {
        return [];
    }
}