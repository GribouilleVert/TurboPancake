<?php
namespace TurboModule\Blog\Actions;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use TurboModule\Blog\BlogHelium;
use TurboModule\Blog\Database\Entities\Post;
use TurboModule\Blog\Database\Tables\CategoriesTable;
use TurboModule\Blog\Database\Tables\PostsTable;
use TurboPancake\Actions\CrudAction;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboPancake\Services\Flash;
use TurboPancake\Validator;

final class PostsCrudAction extends CrudAction {

    /**
     * @var CategoriesTable
     */
    private $categoriesTable;

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
    /**
     * @var BlogHelium
     */
    private $helium;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostsTable $table,
        CategoriesTable $categoriesTable,
        Flash $flash,
        BlogHelium $helium
    ) {
        $table->privateMode = true;
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoriesTable = $categoriesTable;
        $this->helium = $helium;
    }

    /**
     * Suppression d'un élément
     *
     * @param Request $request
     * @return ResponseInterface|string
     * @throws \TurboPancake\Database\Exceptions\NoRecordException
     * @throws \TurboPancake\Database\Exceptions\QueryBuilderException
     */
    public function delete(Request $request)
    {
        /**
         * @var $item Post
         */
        $item = $this->table->find($request->getAttribute('id'));
        if ($item->image) {
            $this->helium->delete($item->image);
        }
        return parent::delete($request);
    }

    /**
     * Récupère les champs compatibles dans la requête
     *
     * @param Request $request
     * @param Post $item
     * @return array
     */
    protected function getFields(Request $request, $item): array
    {
        $fields = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        $fields['image'] = $this->helium->upload($fields['image'], $item->image);
        if (is_null($fields['image'])) {
            $fields['image'] = $item->image;
        }

        $fields['private'] = isset($fields['private']);

        $fields =  array_filter($fields, function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at', 'category_id', 'image', 'private']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($fields, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Crée le validateur et l'initialise
     *
     * @param Request $request
     * @param mixed $itemDatas Données du l'élément traité
     * @return Validator
     * @throws \Exception
     */
    protected function getValidator(Request $request, $itemDatas): Validator
    {
        $validator = (parent::getValidator($request, $itemDatas))
            ->setCustomName('name', 'titre')
            ->setCustomName('slug', 'uri')
            ->setCustomName('content', 'contenu')
            ->setCustomName('created_at', 'date de création')
            ->setCustomName('category_id', 'catégorie')

            ->filled('name', 'slug', 'content', 'created_at')
            ->length('content', 100)
            ->length('name', 4, 250)
            ->exists('category_id', $this->categoriesTable)
            ->length('slug', 3, 60)
            ->dateTime('created_at')
            ->slug('slug')
            ->unique('slug', $this->table, 'slug', [$itemDatas->slug])
            ->unique('name', $this->table, 'name', [$itemDatas->name])
            ->matchMimes('image', '%image/*%', 'Le fichier doit être une image.')
            ->dimensions('image', 350, 350, 'L\'image doit faire au moins 350px par 350px');

        if ($request->getMethod() === "POST") {
            $validator->uploaded('image');
        }

        return $validator;
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

    /**
     * Renvoie les données pour les vues
     *
     * @param array $datas
     * @return array
     */
    protected function viewDatas(array $datas): array
    {
        $datas['categories'] = $this->categoriesTable->findList();
        return $datas;
    }

}
