<?php
namespace TurboPancake\Auth\Identity;

use TurboPancake\Auth\UserInterface;

interface IdentityCheckerInterface {

    /**
     * Définit l'utilisateur sur lequel la fonction dois effectuer des tests
     *
     * @param UserInterface $user
     * @return $this
     */
    public function withUser(UserInterface $user): self;

    /**
     * Définit l'utilisateur sur lequel la fonction dois effectuer des tests
     *
     * @param string $identifier
     * @return $this
     */
    public function withIdentifier(string $identifier): self;

    /**
     * Definit le mot de passe a verifier
     * (Ecrase le mot de passe precedent s'il existe)
     *
     * @param string $rawPassword
     * @return $this
     */
    public function withPassword(string $rawPassword): self;

    /**
     * Verifie si les conditions d'entrée correspondent a un utilisateur valide
     *
     * @return bool
     */
    public function check(): bool;

}