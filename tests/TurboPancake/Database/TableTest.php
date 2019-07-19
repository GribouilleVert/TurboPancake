<?php
namespace Tests\TurboPancake\Database;

use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use TurboPancake\Database\Table;

class TableTest extends TestCase {

    /**
     * @var Table
     */
    private $table;

    public function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:', null, null);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        $pdo->exec("
            CREATE TABLE test (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255)
            );
        ");

        $this->table = new Table($pdo);
        $reflection = new ReflectionClass($this->table);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($this->table, 'test');
    }

    public function testFind()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 1');");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 2');");

        $item = $this->table->find(1);

        $this->assertInstanceOf(\stdClass::class, $item);
        $this->assertEquals('elem 1', $item->name);
    }

    public function testFindList()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 1');");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 2');");

        $items = $this->table->findList();

        $this->assertIsArray($items);
        $this->assertEquals([
            '1' => 'elem 1',
            '2' => 'elem 2'
        ], $items);
    }

    public function testExists()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 1');");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 2');");

        $this->assertTrue($this->table->exists(1));
        $this->assertFalse($this->table->exists(42));
    }

    public function testFindAll()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 1');");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 2');");

        $items = $this->table->findAll();

        $this->assertIsArray($items);
        $this->assertCount(2, $items);
        $this->assertInstanceOf(stdClass::class, $items[0]);

        $this->assertEquals('elem 1', $items[0]->name);
        $this->assertEquals('elem 2', $items[1]->name);
    }

    public function testFindBy()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 1');");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 2');");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem 2');");

        $items = $this->table->findBy('name', 'elem 2');

        $this->assertIsArray($items);
        $this->assertCount(2, $items);
        $this->assertInstanceOf(stdClass::class, $items[0]);

        $this->assertEquals('elem 2', $items[1]->name);
    }

    public function testCount()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('elem $i');");
        }

        $count = $this->table->count();

        $this->assertIsInt($count);
        $this->assertEquals(25, $count);
    }

}