<?php
namespace Framework;

class Renderer {

    const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * @var array
     */
    private $paths = [];

    /**
     * @var array
     */
    private $globals = [];

    /**
     * @param string $namespace
     * @param string|null $path
     */
    public function addPath(string $path, ?string $namespace = null): void
    {
        if (is_null($namespace))
            $this->paths[self::DEFAULT_NAMESPACE] = $path;
        else $this->paths[$namespace] = $path;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**
     * @param string $view
     * @param array $parameters
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
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }

}
