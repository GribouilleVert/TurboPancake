<?php
namespace Tests\TurboPancake\Database;

use TurboModule\Blog\Database\Entities\Post;
use TurboPancake\Database\PaginatedQuery;
use Tests\DatabaseTestCase;
use TurboPancake\Database\Query;
use TurboPancake\Database\QueryResult;

class PaginatedQueryTest extends DatabaseTestCase {

    /**
     * @var PaginatedQuery
     */
    private $paginatedQuery;

    /**
     * @var Query
     */
    private $query;

    public function setUp(): void
    {
        $pdo = $this->getPdo();
        $this->manager = $this->getManager($pdo);
        $this->seedDatabase($pdo, $this->manager);
        $this->query = (new Query($pdo))
            ->table('posts');
        $this->paginatedQuery = new PaginatedQuery($this->query);
    }

    public function testNbResult()
    {
        $this->assertEquals(100, $this->paginatedQuery->getNbResults());
    }

    public function testSlice() {
        $result = $this->paginatedQuery->getSlice(0, 1);
        $this->assertInstanceOf(QueryResult::class, $result);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $result);
    }

    public function testSliceWithEntity() {
        $this->query->using(Post::class);
        $paginatedQuery = new PaginatedQuery($this->query);
        $result = $paginatedQuery->getSlice(0, 1);

        $this->assertInstanceOf(QueryResult::class, $result);
        $this->assertContainsOnlyInstancesOf(Post::class, $result);
    }

}