<?php
namespace Haifunime\Blog;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Haifunime\Blog\Actions\BlogActions;

class BlogModule extends Module {

    /**
     * Configuration du conteneur de dépendances
     */
    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * Configuration de la base de données
     */
    const MIGRATIONS = __DIR__ . '/database/migrations';

    /**
     * Configuration des seeds
     */
    const SEEDS = __DIR__ . '/database/seeds';

    /**
     * BlogModule constructor.
     * @param string $prefix
     * @param Router $router
     * @param RendererInterface $renderer
     */
    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'blog');
        $router->get($prefix, BlogActions::class, 'blog.index');
        $router->get($prefix . '/{slug:[a-z0-9\-]+}-{id:\d+}', BlogActions::class, 'blog.show');
    }

}
