<?php
namespace Tests\Framework;

use GuzzleHttp\Psr7\ServerRequest;
use Framework\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {

    /**
     * @var Router
     */
    private $router;

    public function setUp(): void
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');

        $this->router->get('/blog', function () { return 'Le blog !'; }, 'blog');
        $route = $this->router->match($request);

        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('Le blog !', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIf404()
    {
        $request = new ServerRequest('GET', '/this-is-surely-a-404');

        $this->router->get('/blog', function () { return 'Le blog !'; }, 'blog');
        $route = $this->router->match($request);

        $this->assertNull($route);
    }

    public function testGetMethodWithParameters()
    {
        $request = new ServerRequest('GET', '/blog/gribouille-violet-18');

        $this->router->get('/blog', function () { return 'Le blog !'; }, 'posts');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'Un article !'; }, 'post.show');

        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('Un article !', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'gribouille-violet', 'id' => '18'], $route->getParams());

        //Invalid url
        $route = $this->router->match(new ServerRequest('GET', '/blog/gribouille-violet_18'));
        $this->assertNull($route);
    }

    public function testGenerateUri()
    {
        $this->router->get('/blog', function () { return 'Le blog !'; }, 'posts');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'Un article !'; }, 'post.show');

        $uri = $this->router->generateUri('post.show', ['slug' => 'gribouille-vert', 'id' => '7']);

        $this->assertEquals('/blog/gribouille-vert-7', $uri);
    }

}