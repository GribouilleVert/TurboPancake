<?php
namespace TurboModule\Blog\Actions;

use TurboPancake\Actions\RouterAwareAction;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboModule\Blog\Database\Tables\PostsTable;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostShowAction {

    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var Router
     */
    private $router;

    /**
     * @var PostsTable
     */
    private $postTable;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, Router $router, PostsTable $postTable)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
    }

    public function __invoke(Request $request)
    {
        $post = $this->postTable->findWithCategory($request->getAttribute('id'));
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
