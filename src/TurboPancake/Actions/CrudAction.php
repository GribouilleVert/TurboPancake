<?php
namespace TurboPancake\Actions;

use Psr\Http\Message\ResponseInterface;
use stdClass;
use TurboPancake\Database\Table;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboPancake\Services\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;
use TurboPancake\Validator;

class CrudAction {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var array
     */
    protected $messages = [
        "not found" => "Cet element n'existe pas.",
        "edit" => "L'élément a bien été modifié.",
        "create" => "L'élément a bien été crée.",
        "delete" => "L'élément a bien été supprimé.",
    ];

    use RouterAwareAction;

    /**
     * CrudAction constructor.
     *
     * @param RendererInterface $renderer
     * @param Router $router
     * @param Table $table
     * @param FlashService $flash
     */
    public function __construct(RendererInterface $renderer, Router $router, Table $table, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

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
     * Liste des élément
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? 1;
        $items = $this->table->findPaginated(8, $page);

        if (is_null($items)) {
            return $this->temporaryRedirect($this->routePrefix . '.index');
        }

        return $this->renderer->render(
            $this->viewPath . '/index',
            $this->viewDatas(compact('items', 'page'))
        );
    }

    /**
     * Edition d'un élément
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $errors = null;
        $item = $this->table->find($request->getAttribute('id'));

        if (is_null($item)) {
            $this->flash->warning($this->messages['not found']);
            return $this->temporaryRedirect($this->routePrefix . '.index');
        }

        if ($request->getMethod() === 'PUT') {
            $fields = $this->getFields($request);
            $validator = $this->getValidator($request, $item);
            if ($validator->check()) {
                $this->table->update($item->id, $fields);
                $this->flash->success($this->messages['edit']);
                return $this->temporaryRedirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $fields['id'] = $item->id;
            $item = $fields;
        }

        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->viewDatas(compact('item', 'errors'))
        );
    }

    /**
     * Creation d'un élément
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $errors = null;
        $item = $this->getDefaultEntity();
        if ($request->getMethod() === 'POST') {
            $fields = $this->getFields($request);
            $validator = $this->getValidator($request);
            if ($validator->check()) {
                $this->table->insert($fields);
                $this->flash->success($this->messages['create']);
                return $this->temporaryRedirect($this->routePrefix . '.index');
            }
            $item = $fields;
            $errors = $validator->getErrors();
        }

        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->viewDatas(compact('item', 'errors'))
        );
    }

    /**
     * Suppression d'un élément
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function delete(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id'));
        $this->table->delete($item->id);
        $this->flash->success($this->messages['delete']);
        return $this->temporaryRedirect($this->routePrefix . '.index');
    }

    /**
     * Récupère les champs compatibles dans la requête
     *
     * @param Request $request
     * @return array
     */
    protected function getFields(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Crée le validateur et l'initialise
     *
     * @param Request $request
     * @param null $itemDatas
     * @return Validator
     */
    protected function getValidator(Request $request, $itemDatas = null): Validator
    {
        return new Validator($request->getParsedBody());
    }

    /**
     * Permet d'instancier une entitée vide pour la création
     *
     * @return \stdClass
     */
    protected function getDefaultEntity()
    {
        return new \stdClass();
    }

    /**
     * Renvoie les données pour les vues
     *
     * @param array $datas
     * @return array
     */
    protected function viewDatas(array $datas): array
    {
        return $datas;
    }

}
