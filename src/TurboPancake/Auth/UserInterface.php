<?php
namespace TurboPancake\Auth;

interface UserInterface {

    /**
     * @return string L'identifiant de l'utilisateur sous forme de chaine de caractère
     */
    public function getId(): string;

    /**
     * @return string Le nom d'utilisateur
     */
    public function getUsername(): string;

    /**
     * @return RoleInterface[] La liste des roles d'un utilisateur
     */
    public function getRoles(): array;
}
