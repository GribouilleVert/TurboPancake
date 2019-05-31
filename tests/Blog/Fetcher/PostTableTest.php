<?php
namespace Tests\Haifunime\Blog;


use Haifunime\Blog\Entity\Post;
use Haifunime\Blog\Fetchers\PostTable;
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

    public function testFindNoRecordFount() {
        $result = $this->postTable->find(-1);
        $this->assertNull($result);
    }

}