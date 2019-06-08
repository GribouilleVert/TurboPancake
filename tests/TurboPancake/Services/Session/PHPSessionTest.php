<?php
namespace TurboPancake\Services\Session;

use PHPUnit\Framework\TestCase;

class PHPSessionTest extends TestCase {

    /**
     * @var PHPSession
     */
    private $session;

    public function setUp(): void
    {
        global $_SESSION;
        $_SESSION = [];
        $this->session = new PHPSession(PHPSession::IGNORE_SESSION_CREATION);
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
        $this->session = new PHPSession(PHPSession::THROW_ERROR_IF_ABSENT);

        $this->expectException(\Exception::class);
        $this->session->set('test', 'Ca marche pas !');
    }

}