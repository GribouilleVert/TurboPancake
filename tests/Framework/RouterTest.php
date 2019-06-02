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

    public function testPostMethod()
    {
        $request = new ServerRequest('POST', '/login');

        $this->router->post('/login', function () { return 'Connexion'; }, 'login');
        $route = $this->router->match($request);

        $this->assertEquals('login', $route->getName());
        $this->assertEquals('Connexion', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testAnonymousPostMethod()
    {
        $request = new ServerRequest('POST', '/login');

        $this->router->post('/login', function () { return 'Connexion'; });
        $route = $this->router->match($request);

        $this->assertEquals('Connexion', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIfNotFound()
    {
        $request = new ServerRequest('GET', '/this-is-surely-a-not-found');

        $this->router->get('/blog', function () { return 'Le blog !'; }, 'blog');
        $route = $this->router->match($request);

        $this->assertNull($route);
    }

    public function testPostMethodIfNotFound()
    {
        $request = new ServerRequest('POST', '/this-is-surely-a-not-found');

        $this->router->post('/random', function () { return 'I like potatoes'; });
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

    public function testPostMethodWithParameters()
    {
        $request = new ServerRequest('POST', '/edit/gribouille-violet-18');

        $this->router->post('/login', function () { return 'Connexion'; }, 'login');
        $this->router->post('/edit/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'Un article !'; }, 'post.edit');

        $route = $this->router->match($request);
        $this->assertEquals('post.edit', $route->getName());
        $this->assertEquals('Un article !', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'gribouille-violet', 'id' => '18'], $route->getParams());

        //Invalid url
        $route = $this->router->match(new ServerRequest('POST', '/blog/gribouille-violet_18'));
        $this->assertNull($route);
    }

    public function testGenerateUri()
    {
        $this->router->get('/blog', function () { return 'Le blog !'; }, 'posts');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'Un article !'; }, 'post.show');

        $uri = $this->router->generateUri('post.show', ['slug' => 'gribouille-vert', 'id' => '7']);

        $this->assertEquals('/blog/gribouille-vert-7', $uri);
    }

    public function testGenerateUriWithQueryParameters()
    {
        $this->router->get('/blog', function () { return 'Le blog !'; }, 'posts');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'Un article !'; }, 'post.show');

        $uri = $this->router->generateUri('post.show', ['slug' => 'gribouille-violet', 'id' => '148'], ['a' => '123']);

        $this->assertEquals('/blog/gribouille-violet-148?a=123', $uri);
    }

}