<?php
namespace TurboModule\Blog\Actions;

use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use TurboModule\Blog\Database\Tables\CategoriesTable;
use TurboPancake\Actions\CrudAction;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboPancake\Services\FlashService;
use TurboPancake\Validator;

final class CategoriesCrudAction extends CrudAction {

    /**
     * @var string
     */
    protected $viewPath = '@blog/admin/categories';

    /**
     * @var string
     */
    protected $routePrefix = 'blog.admin.categories';

    /**
     * @var array
     */
    protected $messages = [
        "not found" => "Cette catégorie n'existe pas.",
        "edit" => "La catégorie a bien été modifiée.",
        "create" => "La catégorie a bien été créee.",
        "delete" => "La catégorie a bien été supprimée.",
    ];

    public function __construct(RendererInterface $renderer, Router $router, CategoriesTable $table, FlashService $flash)
    {
        parent::__construct($renderer, $router, $table, $flash);
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
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Crée le validateur et l'initialise
     *
     * @param Request $request
     * @return Validator
     * @throws \Exception
     */
    protected function getValidator(Request $request): Validator
    {
        return (new Validator($request->getParsedBody()))
            ->setCustomName('name', 'titre')
            ->setCustomName('slug', 'uri')
            ->filled('name', 'slug')
            ->length('name', 4, 250)
            ->length('slug', 3, 60)
            ->slug('slug');
    }
}
