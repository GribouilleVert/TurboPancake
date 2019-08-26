<?php
namespace Tests\TurboPancake\Services;

use Traversable;
use TurboPancake\Services\Neon;
use TurboPancake\Services\Session\ArraySession;
use PHPUnit\Framework\TestCase;

class NeonTest extends TestCase {

    /**
     * @var ArraySession
     */
    private $session;

    /**
     * @var Neon
     */
    private $flash;

    protected function setUp(): void
    {
        $this->session = new ArraySession();
        $this->flash = new Neon($this->session);
    }

    public function testAddFlash() {
        $this->flash->success('Salut');

        $this->assertIsArray( $this->flash->get('success'));
        $this->assertIsArray( $this->flash->get('success')[0]);
        $this->assertEquals('Salut', $this->flash->get('success')[0]['message']);
    }

    public function testSuccessfulFlashDeletionAfterDisplay()
    {
        $this->flash->success('Salut');

        $this->assertCount(1, $this->flash->get('success'));
        $this->assertEquals('Salut', $this->flash->get('success')[0]['message']);

        $this->assertNull($this->session->get('info'));
        $this->assertCount(1, $this->flash->get('success'));
        $this->assertEquals('Salut', $this->flash->get('success')[0]['message']);
    }

    public function testNoTypeReturnNull()
    {
        $this->assertNull($this->flash->get('not found'));
    }

    public function testInfo()
    {
        $this->flash->info('Salut');
        $this->assertCount(1, $this->flash->get('info'));
        $this->assertEquals('Salut', $this->flash->get('info')[0]['message']);
    }

    public function testMessage()
    {
        $this->flash->message('Salut');
        $this->assertCount(1, $this->flash->get('message'));
        $this->assertEquals('Salut', $this->flash->get('message')[0]['message']);
    }

    public function testImportant()
    {
        $this->flash->important('Salut');
        $this->assertCount(1, $this->flash->get('important'));
        $this->assertEquals('Salut', $this->flash->get('important')[0]['message']);
    }

    public function testSuccess()
    {
        $this->flash->success('Salut');
        $this->assertCount(1, $this->flash->get('success'));
        $this->assertEquals('Salut', $this->flash->get('success')[0]['message']);
    }

    public function testWarning()
    {
        $this->flash->warning('Salut');
        $this->assertCount(1, $this->flash->get('warning'));
        $this->assertEquals('Salut', $this->flash->get('warning')[0]['message']);
    }

    public function testError()
    {
        $this->flash->error('Salut');
        $this->flash->warning('Test');
        $this->assertCount(1, $this->flash->get('error'));
        $this->assertEquals('Salut', $this->flash->get('error')[0]['message']);
    }

    public function testFullReturn() {
        $this->flash->error('Salut');
        $this->flash->warning('Test');

        $this->assertCount(2, $this->flash->get());

    }

}