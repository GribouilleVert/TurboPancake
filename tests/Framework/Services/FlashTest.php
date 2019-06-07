<?php
namespace Tests\Framework\Services;

use Framework\Services\FlashService;
use Framework\Services\Session\ArraySession;
use PHPUnit\Framework\TestCase;

class FlashTest extends TestCase {

    /**
     * @var ArraySession
     */
    private $session;

    /**
     * @var FlashService
     */
    private $flash;

    protected function setUp(): void
    {
        $this->session = new ArraySession();
        $this->flash = new FlashService($this->session);
    }

    public function testAddFlash() {
        $this->flash->success('Salut');

        $this->assertEquals('Salut', $this->flash->get('success'));
    }

    public function testSuccessfulFlashDeletionAfterDisplay()
    {
        $this->flash->success('Salut');

        $this->assertEquals('Salut', $this->flash->get('success'));
        $this->assertNull($this->session->get('flash'));
        $this->assertEquals('Salut', $this->flash->get('success'));
    }

    public function testNoTypeReturnNull()
    {
        $this->assertNull($this->flash->get('not found'));
    }

    public function testInfo()
    {
        $this->flash->info('Salut');
        $this->assertEquals('Salut', $this->flash->get('info'));
    }

    public function testMessage()
    {
        $this->flash->message('Salut');
        $this->assertEquals('Salut', $this->flash->get('message'));
    }

    public function testImportant()
    {
        $this->flash->important('Salut');
        $this->assertEquals('Salut', $this->flash->get('important'));
    }

    public function testSuccess()
    {
        $this->flash->success('Salut');
        $this->assertEquals('Salut', $this->flash->get('success'));
    }

    public function testWarning()
    {
        $this->flash->warning('Salut');
        $this->assertEquals('Salut', $this->flash->get('warning'));
    }

    public function testError()
    {
        $this->flash->error('Salut');
        $this->assertEquals('Salut', $this->flash->get('error'));
    }

}