<?php
namespace Haifunime\Blog;

use Framework\Router;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule {

    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z0-9\-]+}', [$this, 'show'], 'blog.show');
    }

    public function index(Request $request): string
    {
        return '<h1>Bienvenue sur le blog !</h1>';
    }

    public function show(Request $request): string
    {
        return '<h1>Bienvenue sur l\'article ' . $request->getAttribute('slug') . ' !</h1>';
    }

}
