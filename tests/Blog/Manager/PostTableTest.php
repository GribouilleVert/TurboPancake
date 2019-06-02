<?php
namespace Tests\Haifunime\Blog\Manager;


use Haifunime\Blog\Entity\Post;
use Haifunime\Blog\Managers\PostTable;
use Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase {

    /**
     * @var PostTable
     */
    private $postTable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postTable = new PostTable($this->pdo);
    }

    public function testSimpleFind() {
        $this->seedDatabase();
        $result = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $result);
    }

    public function testNoRecordFount() {
        $result = $this->postTable->find(-1);
        $this->assertNull($result);
    }

    public function testUpdate() {
        $this->seedDatabase();
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
            'slug' => 'cuisson-patates-douces'
        ]);

        $post = $this->postTable->find(1);
        $this->assertEquals('Comment faire cuir des patates douces ?', $post->name);
        $this->assertEquals('cuisson-patates-douces', $post->slug);
    }

    public function testDelete() {
        $this->seedDatabase();

        $count = $this->pdo->query("SELECT count(id) FROM posts")->fetchColumn();
        $this->assertEquals(100, (int) $count);

        $this->postTable->delete(1);

        $count = $this->pdo->query("SELECT count(id) FROM posts")->fetchColumn();
        $this->assertEquals(99, (int) $count);
    }

}