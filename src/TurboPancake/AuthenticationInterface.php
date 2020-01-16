<?php
namespace TurboPancake;

use TurboPancake\Auth\Exceptions\NotLoggedException;
use TurboPancake\Auth\UserInterface;

interface AuthenticationInterface {

    /**
     * Renvoie l'utilisateur actif, si l'utilisateur n'est pas connecté renvoie
     * une NotLoggedExcpetion
     *
     * @return UserInterface
     * @throws NotLoggedException
     */
    public function getUser(): UserInterface;

    /**
     * Indique si l'utilisateur est connecté
     * @return bool
     */
    public function isLogged(): bool;

    /**
     * Permet de connecter l'utisateur (session)
     * N'EFFECTUE PAS VERIFICATION
     *
     * @param string $identifier
     * @param array $options Options de connexion
     * @return UserInterface|null Renvoi null si l'utilisateur n'existe pas
     */
    public function login(string $identifier, array $options = []): ?UserInterface;

    /**
     * Permet de déconnecter la sessions
     */
    public function logout(): void;
}
