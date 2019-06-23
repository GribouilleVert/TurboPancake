<?php
namespace TurboModule\Blog\Actions;

use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use TurboModule\Blog\Database\Entities\Post;
use TurboModule\Blog\Database\Tables\PostsTable;
use TurboPancake\Actions\CrudAction;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboPancake\Services\FlashService;
use TurboPancake\Validator;

final class PostsCrudAction extends CrudAction {

    /**
     * @var string
     */
    protected $viewPath = '@blog/admin/posts';

    /**
     * @var string
     */
    protected $routePrefix = 'blog.admin.posts';

    /**
     * @var array
     */
    protected $messages = [
        "not found" => "Cet article n'existe pas.",
        "edit" => "L'article a bien été modifié.",
        "create" => "L'article a bien été crée.",
        "delete" => "L'article a bien été supprimé.",
    ];

    public function __construct(RendererInterface $renderer, Router $router, PostsTable $table, FlashService $flash)
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
        $fields =  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($fields, [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
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
            ->setCustomName('content', 'contenu')
            ->setCustomName('created_at', 'date de création')
            ->filled('name', 'slug', 'content', 'created_at')
            ->length('content', 100)
            ->length('name', 4, 250)
            ->length('slug', 3, 60)
            ->slug('slug');
    }


    /**
     * Permet d'instancier une entitée vide pour la création
     *
     * @return Post
     * @throws \Exception
     */
    protected function getDefaultEntity()
    {
        $post = new Post();
        $post->created_at = new DateTime();
        return $post;
    }
}
