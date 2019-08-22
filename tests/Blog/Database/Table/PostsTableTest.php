<?php
namespace Tests\TurboModule\Blog\Manager;

use Tests\DatabaseTestCase;
use TurboModule\Blog\Database\Entities\Post;
use TurboModule\Blog\Database\Tables\PostsTable;
use TurboPancake\Database\Exceptions\NoRecordException;

class PostsTableTest extends DatabaseTestCase {

    /**
     * @var PostsTable
     */
    private $postTable;

    protected function setUp(): void
    {
        $this->pdo = $this->getPdo();
        $this->manager = $this->getManager($this->pdo);
        $this->migrateDatabase($this->pdo, $this->manager);
        $this->postTable = new PostsTable($this->pdo);
    }

    public function testSimpleFind() {
        $this->seedDatabase($this->pdo, $this->manager);
        $result = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $result);
    }

    public function testNoRecordFount() {
        $this->expectException(NoRecordException::class);
        $result = $this->postTable->find(-1);
    }

    public function testUpdate() {
        $this->seedDatabase($this->pdo, $this->manager);
        $this->postTable->update(1, [
            'name' => 'Comment faire cuir des haricots verts ?',
            'slug' => 'cuisson-haricots-verts'
        ]);

        $post = $this->postTable->find(1);
        $this->assertEquals('Comment faire cuir des haricots verts ?', $post->name);
        $this->assertEquals('cuisson-haricots-verts', $post->slug);
    }
    
    public function testInsert() {
        $this->postTable->insert([
            'name' => 'Comment faire cuir des patates douces ?',
            'slug' => 'cuisson-patates-douces',
            'created_at' => '0000-00-00 00:00:00',
            'updated_at' => '0000-00-00 00:00:00',
        ]);

        $post = $this->postTable->find(1);
        $this->assertEquals('Comment faire cuir des patates douces ?', $post->name);
        $this->assertEquals('cuisson-patates-douces', $post->slug);
    }

    public function testDelete() {
        $this->seedDatabase($this->pdo, $this->manager);

        $count = $this->pdo->query("SELECT count(id) FROM posts")->fetchColumn();
        $this->assertEquals(100, (int) $count);

        $this->postTable->delete(1);

        $count = $this->pdo->query("SELECT count(id) FROM posts")->fetchColumn();
        $this->assertEquals(99, (int) $count);
    }

}