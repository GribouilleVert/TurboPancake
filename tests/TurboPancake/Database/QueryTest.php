<?php
namespace Tests\TurboPancake\Database;

use Tests\DatabaseTestCase;
use Tests\TurboPancake\Database\Entities\Demo;
use TurboPancake\Database\Exceptions\QueryBuilderException;
use TurboPancake\Database\Query;
use TurboPancake\Database\QueryResult;

class QueryTest extends DatabaseTestCase {

    public function testSimpleQuery() {
        $query = (new Query)
            ->table('posts')
            ->select('name');

        $this->assertEquals("SELECT name FROM posts", (string)$query);
    }

    public function testQueryWithWhereDirective() {
        $query = (new Query)
            ->table('posts', 'p')
            ->select('id', 'name')
            ->where('a = :a OR b = :b', 'c = 1');

        $this->assertEquals("SELECT id, name FROM posts as p WHERE (a = :a OR b = :b) AND (c = 1)", (string)$query);

        $query = (new Query)
            ->table('posts', 'p')
            ->select('id', 'name')
            ->where('a = :a OR b = :b')
            ->where('c = 1');

        $this->assertEquals("SELECT id, name FROM posts as p WHERE (a = :a OR b = :b) AND (c = 1)", (string)$query);
    }

    public function testQueryWithGroupByDirective() {
        $query = (new Query)
            ->table('posts', 'p')
            ->select('id', 'name')
            ->groupBy('p.id', 'p.name');

        $this->assertEquals("SELECT id, name FROM posts as p GROUP BY p.id, p.name", (string)$query);

        $query = (new Query)
            ->table('posts', 'p')
            ->select('id', 'name')
            ->groupBy('p.id')
            ->groupBy('p.name');

        $this->assertEquals("SELECT id, name FROM posts as p GROUP BY p.id, p.name", (string)$query);
    }

    public function testQueryWithLimitDirective() {
        $query = (new Query)
            ->table('posts', 'p')
            ->select('id', 'name')
            ->limit(10);

        $this->assertEquals("SELECT id, name FROM posts as p LIMIT 10", (string)$query);

        $query = (new Query)
            ->table('posts', 'p')
            ->select('id', 'name')
            ->limit(10, 5);

        $this->assertEquals("SELECT id, name FROM posts as p LIMIT 5, 10", (string)$query);
    }

    public function testQueryWithOrderByDirective() {
        $query = (new Query)
            ->table('posts', 'p')
            ->select('id', 'name')
            ->order('id DESC');

        $this->assertEquals("SELECT id, name FROM posts as p ORDER BY id DESC", (string)$query);

        $query = (new Query)
            ->table('posts', 'p')
            ->select('id', 'name')
            ->order('id DESC', 'name ASC');

        $this->assertEquals("SELECT id, name FROM posts as p ORDER BY id DESC, name ASC", (string)$query);
    }

    public function testQueryWithJoinDirective() {
        $query = (new Query)
            ->table('posts', 'p')
            ->select('p.id', 'c.name')
            ->join('categories as c', 'c.id = p.category_id');

        $this->assertEquals("SELECT p.id, c.name FROM posts as p LEFT JOIN categories as c ON c.id = p.category_id", (string)$query);

        $query = (new Query)
            ->table('posts', 'p')
            ->select('p.id', 'c.name')
            ->join('categories as c', 'c.id = p.category_id', Query::LEFT_JOIN);

        $this->assertEquals("SELECT p.id, c.name FROM posts as p LEFT JOIN categories as c ON c.id = p.category_id", (string)$query);

        $query = (new Query)
            ->table('posts', 'p')
            ->select('p.id', 'c.name')
            ->join('categories as c', 'c.id = p.category_id', Query::RIGHT_JOIN);

        $this->assertEquals("SELECT p.id, c.name FROM posts as p RIGHT JOIN categories as c ON c.id = p.category_id", (string)$query);

    }

    public function testCount() {
        $pdo = $this->getPdo();
        $manager = $this->getManager($pdo);
        $this->migrateDatabase($pdo, $manager);
        $this->seedDatabase($pdo, $manager);

        $result = (new Query($pdo))
            ->table('posts')
            ->count();

        $this->assertEquals(100, $result);

        $result = (new Query($pdo))
            ->table('posts', 'p')
            ->where('p.id <= :max')
            ->parameters(['max' => 74])
            ->count();

        $this->assertEquals(74, $result);

        $this->expectException(QueryBuilderException::class);
        (new Query)
            ->table('posts', 'p')
            ->count();
    }

    public function testFetch() {
        $pdo = $this->getPdo();
        $manager = $this->getManager($pdo);
        $this->migrateDatabase($pdo, $manager);
        $this->seedDatabase($pdo, $manager);

        $result = (new Query($pdo))
            ->table('posts')
            ->fetch();

        $this->assertInstanceOf(\stdClass::class, $result);

        $result = (new Query($pdo))
            ->table('posts', 'p')
            ->where('p.id = :id')
            ->parameters(['id' => 74])
            ->fetch();

        $this->assertEquals(74, $result->id);

        $this->expectException(QueryBuilderException::class);
        (new Query)
            ->table('posts', 'p')
            ->fetch();
    }

    public function testFetchAll() {
        $pdo = $this->getPdo();
        $manager = $this->getManager($pdo);
        $this->migrateDatabase($pdo, $manager);
        $this->seedDatabase($pdo, $manager);

        $result = (new Query($pdo))
            ->table('posts')
            ->fetchAll();

        $this->assertInstanceOf(QueryResult::class, $result);
        $this->assertCount(100, $result);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $result);

        $result = (new Query($pdo))
            ->table('posts', 'p')
            ->where('p.id >= :min')
            ->parameters(['min' => 74])
            ->fetchAll();

        $this->assertEquals(74, $result[0]->id);

        $this->expectException(QueryBuilderException::class);
        (new Query)
            ->table('posts', 'p')
            ->fetchAll();
    }

    public function testQueryUsingEntityHydratation() {
        $pdo = $this->getPdo();
        $manager = $this->getManager($pdo);
        $this->migrateDatabase($pdo, $manager);
        $this->seedDatabase($pdo, $manager);

        $result = (new Query($pdo))
            ->table('posts', 'p')
            ->using(Demo::class)
            ->fetchAll();

        $this->assertCount(100, $result);
        $this->assertContainsOnlyInstancesOf(Demo::class, $result);
        $this->assertEquals('demo', substr($result[0]->getSlug(), -4));
        $this->assertSame($result[0], $result[0]);
    }
}