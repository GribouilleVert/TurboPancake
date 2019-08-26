<?php
namespace TurboPancake;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Class Module
 * @package TurboPancake
 */
abstract class Module {

    /**
     * Configuration du conteneur de dépendances
     */
    const DEFINITIONS = null;

    /**
     * Dossiers pour la gestion de la base de donnée
     */
    const MIGRATIONS = null;
    const SEEDS = null;

    abstract public function load(): void;

    /**
     * Renvoie les modules qui doivent être presents pour le bon fonctionnement du module actuel
     * @return Module[]
     */
    public function getModuleDependencies(): array {
        return [];
    }

    /**
     * Renvoie les middlewares qui doivent être presents pour le bon fonctionnement du module actuel
     * @return MiddlewareInterface[]
     */
    public function getMiddlewareDependencies(): array {
        return [];
    }

}
