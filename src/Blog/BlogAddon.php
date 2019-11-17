<?php
namespace TurboModule\Blog;

use TurboModule\Administration\AdminAddonInterface;
use TurboModule\Blog\Database\Tables\PostsTable;
use TurboPancake\Renderer\RendererInterface;

class BlogAddon implements AdminAddonInterface {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostsTable
     */
    private $table;

    public function __construct(RendererInterface $renderer, PostsTable $table)
    {
        $this->renderer = $renderer;
        $this->table = $table;
    }

    public function renderWidget(): ?string
    {
        $count = $this->table->count();
        return $this->renderer->render('@blog/admin/widget', compact('count'));
    }

    public function renderMenuItem(): ?string
    {
        return $this->renderer->render('@blog/admin/menu_item');
    }
}
