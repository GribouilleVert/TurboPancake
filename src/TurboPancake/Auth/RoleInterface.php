<?php
namespace TurboPancake\Auth;

interface RoleInterface {

    /**
     * Permet d'obtenuir le nom du role
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Permet d'obtenuir le "niveau' du role,
     * un role doit ecraser les permissions de tout ceux qui
     * ont un niveaux inferieur
     *
     * @return int
     */
    public function getLevel(): int;

    /**
     * Renvoie la liste des permissions sous forme
     *
     * @return mixed[]
     */
    public function getPermissions(): array;

}