<?php
namespace TurboModule\Blog\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use TurboModule\Blog\Database\Tables\CategoriesTable;
use TurboModule\Blog\Database\Tables\PostsTable;
use TurboPancake\Router\RouterAware;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;

final class CategoryShowAction {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var CategoriesTable
     */
    private $categoriesTable;

    /**
     * @var PostsTable
     */
    private $postsTable;

    use RouterAware;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CategoriesTable $categoriesTable,
        PostsTable $postsTable
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->categoriesTable = $categoriesTable;
        $this->postsTable = $postsTable;
    }

    public function __invoke(Request $request)
    {
        $category = $this->categoriesTable->findBy('slug', $request->getAttribute('slug'))[0];


        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? 1;
        $posts = $this->postsTable->findPublicByCategory($category->id)->paginate(9, $page);

        $categories = $this->categoriesTable->findAll();

        return $this->renderer->render('@blog/category', compact('category', 'categories', 'posts', 'page'));
    }

}
