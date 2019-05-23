<?php


namespace Framework\Renderer;

class TwigRenderer implements RendererInterface {

    /**
     * @var \Twig\Loader\FilesystemLoader
     */
    private $loader;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * TwigRenderer constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->loader = new \Twig\Loader\FilesystemLoader($path);
        $this->twig = new \Twig\Environment($this->loader, [
            'debug'         => true,
            'charset'       => 'utf-8',
            'auto_reload'   => true,
        ]);
    }

    /**
     * Permet d'ajouter un chemin d'ou se trouvent les sources
     * @param string $path Le chemin des vues, si $namespace est null, alors ce sera le chemin par défaut
     * @param string|null $namespace Le namespace a associer au chemin
     * @throws \Twig\Error\LoaderError
     */
    public function addPath(string $path, ?string $namespace = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Permet d'ajouter une variable disponible pour toutes les vues
     * @param string $key
     * @param $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * Permet de rendre une vue avec les variables
     * @param string $view Nom de la vue sour la forme [@namespace/]vue
     * @param array $parameters Variables a passer à la vue, écrase les variables globales
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(string $view, array $parameters = []): string
    {
        return $this->twig->render($view . '.twig', $parameters);
    }

}
