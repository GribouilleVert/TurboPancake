<?php
namespace TurboModule\Blog\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use TurboModule\Blog\Database\Tables\CategoriesTable;
use TurboPancake\Actions\CrudAction;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboPancake\Services\Neon;
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

    /**
     * CategoriesCrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param CategoriesTable $table
     * @param Neon $flash
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CategoriesTable $table,
        Neon $flash
    ) {
        parent::__construct($renderer, $router, $table, $flash);
    }

    /**
     * Récupère les champs compatibles dans la requête
     *
     * @param Request $request
     * @param $item
     * @return array
     */
    protected function getFields(Request $request, $item): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Crée le validateur et l'initialise
     *
     * @param Request $request
     * @param null $itemDatas
     * @return Validator
     * @throws \Exception
     */
    protected function getValidator(Request $request, $itemDatas = null): Validator
    {
        return (parent::getValidator($request, $itemDatas))
            ->setCustomName('name', 'titre')
            ->setCustomName('slug', 'URL')
            ->filled('name', 'slug')
            ->length('name', 4, 250)
            ->length('slug', 3, 120)
            ->slug('slug')
            ->unique('slug', $this->table, 'slug', [$itemDatas->slug]);
    }

}
