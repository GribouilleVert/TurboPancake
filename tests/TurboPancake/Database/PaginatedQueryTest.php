<?php
namespace Tests\TurboPancake\Database;

use TurboPancake\Database\PaginatedQuery;
use Haifunime\Blog\Entity\Post;
use Tests\DatabaseTestCase;

class PaginatedQueryTest extends DatabaseTestCase {

    /**
     * @var PaginatedQuery
     */
    private $paginatedQuery;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
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