<?php
namespace TurboModule\Blog\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TurboPancake\Router\RouterAware;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router\Router;
use TurboModule\Blog\Database\Tables\PostsTable;
use Psr\Http\Message\ServerRequestInterface;

final class PostShowAction implements MiddlewareInterface {

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

    use RouterAware;

    public function __construct(RendererInterface $renderer, Router $router, PostsTable $postTable)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $post = $this->postTable->findPublicWithCategory($request->getAttribute('id'));
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

        return new Response(200, [], $this->renderer->render(
            '@blog/show',
            compact('post')
        ));
    }
}
