<?php
namespace Haifunime\Blog\Actions;

use TurboPancake\Actions\RouterAware;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use Haifunime\Blog\Managers\PostTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class BlogActions {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var Router
     */
    private $router;
    
    use RouterAware;

    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
    }

    public function __invoke(Request $request)
    {
        $id = $request->getAttribute('id');
        if (!is_null($id)) {
            return  $this->show($request);
        }
        return $this->index($request);
    }

    public function index(Request $request): string
    {
        $queryParams = $request->getQueryParams();
        $posts = $this->postTable->findPaginated(9, $queryParams['page'] ?? 1);
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function show(Request $request)
    {
        $post = $this->postTable->find($request->getAttribute('id'));
        if (is_null($post)) {
            return $this->temporaryRedirect('blog.index');
        }

        $slug = $request->getAttribute('slug');
        if ($post->slug !== $slug) {
            return $this->temporaryRedirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id,
            ]);
        }
        
        return $this->renderer->render('@blog/show', compact('post'));
    }

}
