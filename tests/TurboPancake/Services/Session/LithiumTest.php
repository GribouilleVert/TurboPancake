<?php
namespace Tests\TurboPancake\Services\Session;

use PHPUnit\Framework\TestCase;
use TurboPancake\Services\Session\Lithium;

class PHPSessionTest extends TestCase {

    /**
     * @var Lithium
     */
    private $session;

    public function setUp(): void
    {
        global $_SESSION;
        $_SESSION = [];
        $this->session = new Lithium(Lithium::IGNORE_SESSION_CREATION);
    }

    public function testGetDefault()
    {
        $this->assertEquals([], $this->session->get('test', []));
    }

    public function testSet()
    {
        $this->assertNull($this->session->get('test'));

        $this->session->set('test', 'Ca marche !');
        $this->assertEquals('Ca marche !', $this->session->get('test'));
    }

    public function testDelete()
    {
        $this->session->set('test', 'Ca marche !');
        $this->session->delete('test');

        $this->assertNull($this->session->get('test'));
    }

    public function testInitWithStrictMode()
    {
        $this->session = new Lithium(Lithium::THROW_ERROR_IF_ABSENT);

        $this->expectException(\Exception::class);
        $this->session->set('test', 'Ca marche pas !');
    }

}