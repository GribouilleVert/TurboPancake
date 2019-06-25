<?php
namespace Tests\TurboPancake\Database;

use TurboModule\Blog\Database\Entities\Post;
use TurboPancake\Database\PaginatedQuery;
use Tests\DatabaseTestCase;

class PaginatedQueryTest extends DatabaseTestCase {

    /**
     * @var PaginatedQuery
     */
    private $paginatedQuery;

    public function setUp(): void
    {
        $this->pdo = $this->getPdo();
        $this->manager = $this->getManager($this->pdo);
        $this->seedDatabase($this->pdo, $this->manager);
        $this->paginatedQuery = new PaginatedQuery(
            $this->pdo,
            "SELECT * FROM posts",
            "SELECT count(id) FROM posts",
        );
    }

    public function testNbResult()
    {
        $this->assertEquals(100, $this->paginatedQuery->getNbResults());
    }

    public function testSlice() {
        $result = $this->paginatedQuery->getSlice(0, 1);
        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $result);
    }

    public function testSliceWithEntity() {
        $paginatedQuery = new PaginatedQuery(
            $this->pdo,
            "SELECT * FROM posts",
            "SELECT count(id) FROM posts",
            Post::class
        );
        $result = $paginatedQuery->getSlice(0, 1);

        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(Post::class, $result);
    }

}