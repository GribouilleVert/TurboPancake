<?php
namespace Tests\TurboPancake;

use mysql_xdevapi\Exception;
use ReflectionClass;
use Tests\DatabaseTestCase;
use TurboPancake\Database\Table;
use TurboPancake\Validator;
use TurboPancake\Validator\ValidationError;

class ValidatorTest extends DatabaseTestCase {

    public function buildValidator(array $fields)
    {
        return new Validator($fields);
    }

    public function testRequiredSuccess()
    {
        $errors = $this->buildValidator([
            'name' => 'John Doe',
            'location' => 'New York'
        ])
            ->required('name', 'location')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testRequiredFail()
    {
        $errors = $this->buildValidator([
            'name' => 'John Doe'
        ])
            ->required('name', 'location')
            ->getErrors();

        $this->assertCount(1, $errors);
        $this->assertInstanceOf(ValidationError::class, $errors['location']);
        $this->assertEquals("Le champ location est requis.", (string)$errors['location']);
    }

    public function testSlugSuccess()
    {
        $errors = $this->buildValidator([
            'slug' => 'this-is-a-valid-slug-101'
        ])
            ->slug('slug')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testSlugFail()
    {
        $errors = $this->buildValidator([
            'slug1' => 'This is definitely not a valid slug !',
            'slug2' => 'this-one-is-valid-though',
            'slug3' => 'this-one-too-or--not',
            'slug4' => 'nope-no-way-',
        ])
            ->slug('slug1')
            ->slug('slug2')
            ->slug('slug3')
            ->getErrors();

        $this->assertCount(2, $errors);
    }

    public function testNotEmptySuccess()
    {
        $errors = $this->buildValidator([
            'field1' => 'Yeah, i\'m not empty',
            'field2' => 'I\'m filled too !',
        ])
            ->filled('field1', 'field2')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testNotEmptyFail()
    {
        $errors = $this->buildValidator([
            'first' => 'Yeah, i\'m not empty',
            'last' => '',
            'location' => '      ',
        ])
            ->filled('first', 'last', 'location')
            ->getErrors();

        $this->assertCount(2, $errors);
    }

    public function testLength()
    {
        $fields = [
            'location' => 'Toulouse'
        ];

        $this->assertCount(0, $errors = $this->buildValidator($fields)->length('location', 4)->getErrors());
        $this->assertCount(1, $errors = $this->buildValidator($fields)->length('location', 10)->getErrors());
        $this->assertCount(1, $errors = $this->buildValidator($fields)->length('location', 4, 6)->getErrors());
        $this->assertCount(0, $errors = $this->buildValidator($fields)->length('location', 4, 10)->getErrors());
        $this->assertCount(1, $errors = $this->buildValidator($fields)->length('location', null, 6)->getErrors());
        $this->assertCount(0, $errors = $this->buildValidator($fields)->length('location', null, 10)->getErrors());

        $this->expectException(\Exception::class);
        $this->assertCount(0, $errors = $this->buildValidator($fields)->length('location', 2, 1)->getErrors());

        $this->expectException(\Exception::class);
        $this->assertCount(0, $errors = $this->buildValidator($fields)->length('location', null, null)->getErrors());
    }

    public function testDateTime()
    {
        $this->assertCount(0, $this->buildValidator(['date'  => '2003-03-18 10:30:15'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->buildValidator(['date'  => '2003-03-18'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->buildValidator(['date'  => '10:30:15'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->buildValidator(['date'  => '2008-15-18'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->buildValidator(['date'  => '2003-02-29'])->dateTime('date')->getErrors());
    }

    public function testRegex()
    {
        $this->assertTrue($this->buildValidator(['test' => 'a-e-w-k'])->regex('test', '/^([a-z]-){3}[a-z]$/')->check());
    }

    public function testExists()
    {
        $pdo = $this->getPdo();
        $pdo->exec("
            CREATE TABLE test (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255)
            );
        ");
        $pdo->exec("INSERT INTO test (name) VALUES ('elem 1')");
        $pdo->exec("INSERT INTO test (name) VALUES ('elem 2')");

        $table = new Table($pdo);
        $reflection = new ReflectionClass($table);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($table, 'test');

        $this->assertTrue($this->buildValidator(['test' => 1])->exists('test', $table)->check());
        $this->assertFalse($this->buildValidator(['test' => 48])->exists('test', $table)->check());

        $this->assertTrue($this->buildValidator(['test' => 42])->exists('test', [42, 'a', true])->check());
        $this->assertTrue($this->buildValidator(['test' => 'a'])->exists('test', [42, 'a', true])->check());
        $this->assertFalse($this->buildValidator(['test' => false])->exists('test', [42, 'a', true])->check());

        $this->expectException(\Exception::class);
        $this->assertFalse($this->buildValidator(['test' => false])->exists('test', 'This will trigger an exception')->check());
    }

}