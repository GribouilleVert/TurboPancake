<?php
namespace Haifunime\Blog;

use DI\Container;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Haifunime\Blog\Actions\AdminBlogActions;
use Haifunime\Blog\Actions\BlogActions;
use Psr\Container\ContainerInterface;

final class BlogModule extends Module {

    /**
     * Configuration du conteneur de dépendances
     */
    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * Configuration de la base de données
     */
    const MIGRATIONS = __DIR__ . '/Database/migrations';

    /**
     * Configuration des seeds
     */
    const SEEDS = __DIR__ . '/Database/seeds';

    /**
     * BlogModule constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath(__DIR__ . '/views', 'blog');

        $router = $container->get(Router::class);

        $router->get($container->get('blog.prefix'), BlogActions::class, 'blog.index');
        $router->get($container->get('blog.prefix') . '/{slug:[a-z0-9\-]+}-{id:\d+}', BlogActions::class, 'blog.show');

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", AdminBlogActions::class, 'blog.admin');
        }
    }

}
