<?php
namespace TurboModule\Blog\Actions;

use TurboPancake\Actions\RouterAware;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboPancake\Services\FlashService;
use TurboModule\Blog\Managers\PostTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AdminBlogActions {

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

    /**
     * @var FlashService
     */
    private $flash;

    use RouterAware;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }

        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }

        $id = $request->getAttribute('id');
        if (!is_null($id)) {
            return  $this->edit($request);
        }

        return $this->index($request);
    }

    /**
     * Liste des articles
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $queryParams = $request->getQueryParams();
        $items = $this->postTable->findPaginated(8, $queryParams['page'] ?? 1);

        return $this->renderer->render('@blog/admin/index', compact('items'));
    }

    /**
     * Edition d'un article
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->postTable->find($request->getAttribute('id'));

        if ($request->getMethod() === 'PUT') {
            $fields = $this->getFields($request);
            $fields['updated_at'] = date('Y-m-d H:i:s');
            $this->postTable->update($item->id, $fields);
            $this->flash->success('L\'article a bien été modifié');
            return $this->temporaryRedirect('blog.admin.index');
        }

        return $this->renderer->render('@blog/admin/edit', compact('item'));
    }

    /**
     * Creation d'un article
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $fields = $this->getFields($request);
            $fields = array_merge($fields, [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $this->postTable->insert($fields);
            return $this->temporaryRedirect('blog.admin.index');
        }

        return $this->renderer->render('@blog/admin/create');
    }

    /**
     * Suppression d'un article
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function delete(Request $request)
    {
        $item = $this->postTable->find($request->getAttribute('id'));
        $this->postTable->delete($item->id);
        return $this->temporaryRedirect('blog.admin.index');
    }

    /**
     * Récupère les champs compatibles dans la requête
     * @param Request $request
     * @return array
     */
    private function getFields(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

}
