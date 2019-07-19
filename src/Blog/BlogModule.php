<?php
namespace TurboModule\Blog;

use TurboModule\Blog\Actions\CategoriesCrudAction;
use TurboModule\Blog\Actions\CategoryShowAction;
use TurboModule\Blog\Actions\PostsIndexAction;
use TurboPancake\Module;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboModule\Blog\Actions\PostsCrudAction;
use TurboModule\Blog\Actions\PostShowAction;
use Psr\Container\ContainerInterface;

final class BlogModule extends Module {

    /**
     * Configuration du conteneur de dépendances
     */
    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * Configuration de la base de données
     */
    const MIGRATIONS = __DIR__ . '/Database/mgmt/migrations';

    /**
     * Configuration des seeds
     */
    const SEEDS = __DIR__ . '/Database/mgmt/seeds';

    /**
     * BlogModule constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath(__DIR__ . '/views', 'blog');

        $router = $container->get(Router::class);

        $router->get($container->get('blog.prefix'), PostsIndexAction::class, 'blog.index');
        $router->get(
            $container->get('blog.prefix') . '/{slug:[a-z0-9\-]+}-{id:\d+}',
            PostShowAction::class,
            'blog.show'
        );
        $router->get(
            $container->get('blog.prefix') . '/category/{slug:[a-z0-9\-]+}',
            CategoryShowAction::class,
            'blog.category'
        );

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostsCrudAction::class, 'blog.admin.posts');
            $router->crud("$prefix/categories", CategoriesCrudAction::class, 'blog.admin.categories');
        }
    }

}
