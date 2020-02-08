<?php
namespace TurboPancake;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Auth\Exceptions\NotLoggedException;
use TurboPancake\Auth\UserInterface;

interface AuthenticationInterface {

    /**
     * Renvoie l'utilisateur actif, si l'utilisateur n'est pas connecté renvoie
     * une NotLoggedExcpetion
     *
     * @param ServerRequestInterface|null $request If null use global vars instead
     * @return UserInterface
     * @throws NotLoggedException
     */
    public function getUser(?ServerRequestInterface $request = null): UserInterface;

    /**
     * Indique si l'utilisateur est connecté
     *
     * @param ServerRequestInterface|null $request If null use global vars instead
     * @return bool
     */
    public function isLogged(?ServerRequestInterface $request = null): bool;

    /**
     * Permet de connecter l'utisateur (session)
     * N'EFFECTUE PAS VERIFICATION
     *
     * @param string $identifier
     * @param ResponseInterface $reponse Réponse à modifier
     * @param array $options Options de connexion
     * @return UserInterface|null Renvoi null si l'utilisateur n'existe pas
     */
    public function login(string $identifier, ResponseInterface &$reponse, array $options = []): ?UserInterface;

    /**
     * Permet de déconnecter la sessions
     *
     * @param ResponseInterface $reponse Réponse à modifier
     */
    public function logout(ResponseInterface &$reponse): void;
}
