<?php
namespace TurboModule\Blog\Actions;

use TurboModule\Blog\Database\Tables\CategoriesTable;
use TurboPancake\Renderer\RendererInterface;
use TurboModule\Blog\Database\Tables\PostsTable;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostsIndexAction {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostsTable
     */
    private $postsTable;

    /**
     * @var CategoriesTable
     */
    private $categoiesTable;

    public function __construct(RendererInterface $renderer, PostsTable $postTable, CategoriesTable $categoiesTable)
    {
        $this->renderer = $renderer;
        $this->postsTable = $postTable;
        $this->categoiesTable = $categoiesTable;
    }

    public function __invoke(Request $request)
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? 1;
        $posts = $this->postsTable->findPublic()->paginate(9, $page);
        $categories = $this->categoiesTable->findAll();

        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'page'));
    }

}
