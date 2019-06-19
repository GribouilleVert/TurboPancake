<?php
namespace TurboModule\Blog\Actions;

use TurboPancake\Actions\RouterAware;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboPancake\Services\FlashService;
use TurboModule\Blog\Managers\PostTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use TurboPancake\Validator;

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
        $errors = null;

        if (is_null($item)) {
            $this->flash->warning('Cet article n\'existe pas');
            return $this->temporaryRedirect('blog.admin.index');
        }

        if ($request->getMethod() === 'PUT') {
            $fields = $this->getFields($request);
            $fields['updated_at'] = date('Y-m-d H:i:s');
            $validator = $this->getValidator($request);
            if ($validator->check()) {
                $this->postTable->update($item->id, $fields);
                $this->flash->success('L\'article a bien été modifié');
                return $this->temporaryRedirect('blog.admin.index');
            }
            $errors = $validator->getErrors();


            $fields['id'] = $item->id;
            $item = $fields;
        }


        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
    }

    /**
     * Creation d'un article
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $errors = null;
        $item = null;
        if ($request->getMethod() === 'POST') {
            $fields = $this->getFields($request);
            $validator = $this->getValidator($request);
            if ($validator->check()) {
                $this->postTable->insert($fields);
                $this->flash->success('L\'article à bien été créé');
                return $this->temporaryRedirect('blog.admin.index');
            }
            $errors = $validator->getErrors();
            $item = $fields;
        }

        return $this->renderer->render('@blog/admin/create', compact('item', 'errors'));
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
        $fields =  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($fields, [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function getValidator(Request $request): Validator
    {
        return (new Validator($request->getParsedBody()))
            ->setCustomName('name', 'titre')
            ->setCustomName('slug', 'uri')
            ->setCustomName('content', 'contenu')
            ->filled('name', 'slug', 'content')
            ->length('content', 100)
            ->length('name', 4, 250)
            ->length('slug', 3, 60)
            ->slug('slug');
    }

}
