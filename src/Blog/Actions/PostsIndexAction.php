<?php
namespace TurboModule\Blog\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboModule\Blog\Database\Tables\CategoriesTable;
use TurboPancake\Renderer\RendererInterface;
use TurboModule\Blog\Database\Tables\PostsTable;

final class PostsIndexAction implements MiddlewareInterface {

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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? 1;
        $posts = $this->postsTable->findPublic()->paginate(9, $page);
        $categories = $this->categoiesTable->findAll();

        return new Response(200, [], $this->renderer->render(
            '@blog/index',
            compact('posts', 'categories', 'page')
        ));
    }
}
