<?php
namespace Haifunime\Blog\Actions;

use Framework\Actions\RouterAware;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use \PDO;

class BlogActions {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var Router
     */
    private $router;
    
    use RouterAware;

    public function __construct(RendererInterface $renderer, PDO $pdo, Router $router)
    {
        $this->renderer = $renderer;
        $this->pdo = $pdo;
        $this->router = $router;
    }

    public function __invoke(Request $request)
    {
        $slug = $request->getAttribute('slug');
        if (!is_null($slug)) {
            return  $this->show($request);
        }
        return $this->index();
    }

    public function index(): string
    {
        $posts = $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')
            ->fetchAll();
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function show(Request $request)
    {
        $query = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$request->getAttribute('id')]);
        $post = $query->fetch();

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
