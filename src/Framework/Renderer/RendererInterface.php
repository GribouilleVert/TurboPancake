<?php

namespace Framework\Renderer;

Interface RendererInterface {

    /**
     * Permet d'ajouter un chemin d'ou se trouvent les sources
     * @param string $path Le chemin des vues, si $namespace est null, alors ce sera le chemin par défaut
     * @param string|null $namespace Le namespace a associer au chemin
     */
    public function addPath(string $path, ?string $namespace = null): void;

    /**
     * Permet d'ajouter une variable disponible pour toutes les vues
     * @param string $key
     * @param $value
     */
    public function addGlobal(string $key, $value): void;

    /**
     * Permet de rendre une vue avec les variables
     * @param string $view Nom de la vue sour la forme [@namespace/]vue
     * @param array $parameters Variables a passer à la vue, écrase les variables globales
     * @return string
     */
    public function render(string $view, array $parameters = []): string;

}
