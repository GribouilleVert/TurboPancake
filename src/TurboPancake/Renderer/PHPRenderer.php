<?php
namespace TurboPancake\Renderer;

class PHPRenderer implements RendererInterface {

    const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * @var array Liste des chemins du renderer
     */
    private $paths = [];

    /**
     * @var array Variables globales
     */
    private $globals = [];

    /**
     * Renderer constructor.
     * @param string|null $defaultPath Chemin par défaut, reviens a faire un addPath('path'). Si null, alors ignoré
     */
    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Permet d'ajouter un chemin d'ou se trouvent les sources
     * @param string $path Le chemin des vues, si $namespace est null, alors ce sera le chemin par défaut
     * @param string|null $namespace Le namespace a associer au chemin
     */
    public function addPath(string $path, ?string $namespace = null): void
    {
        if (is_null($namespace)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $path;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Permet d'ajouter une variable disponible pour toutes les vues
     * @param string $key
     * @param $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**$string
     * Permet de rendre une vue avec les variables
     * @param string $view Nom de la vue sour la forme [@namespace/]vue
     * @param array $parameters Variables a passer à la vue, écrase les variables globales
     * @return string
     */
    public function render(string $view, array $parameters = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }

        ob_start();
        extract($this->globals);
        extract($parameters);
        require $path;
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Permet de détecter la présence d'un namespace dans une chaine de caractère
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * Permet d'extraire le namespace d'une chain de caractère
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * Permet de remplacer le namespace par le chemin
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }

}
