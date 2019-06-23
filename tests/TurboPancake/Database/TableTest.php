<?php
namespace Tests\TurboPancake\Database;

use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
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

}