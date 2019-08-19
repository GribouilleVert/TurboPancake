<?php
namespace Tests\TurboPancake\Services;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use TurboPancake\Services\Helium;

class HeliumTest extends TestCase {

    /**
     * @var Helium
     */
    private $helium;

    public function setUp(): void
    {
        $this->helium = new Helium(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
        mkdir(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
    }

    public function tearDown(): void
    {
        foreach (scandir(__DIR__ . DIRECTORY_SEPARATOR . 'tmp') as $file) {
            if (!in_array($file, ['.', '..'])) {
                unlink(__DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $file);
            }
        }
        rmdir(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
    }

    public function testUpload()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)
            ->getMock();

        $uploadedFile
            ->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('photo.png');

        $uploadedFile
            ->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo(__DIR__ . DIRECTORY_SEPARATOR . 'tmp/photo.png'));


        $result = $this->helium->upload($uploadedFile);
        $this->assertEquals('photo.png', $result);
    }


    public function testUploadWithExistingFile()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)
            ->getMock();

        $uploadedFile
            ->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('photo.png');

        touch(__DIR__ . DIRECTORY_SEPARATOR . 'tmp/photo.png');

        $uploadedFile
            ->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo(__DIR__ . DIRECTORY_SEPARATOR . 'tmp/photo_copy.png'));


        $result = $this->helium->upload($uploadedFile);
        $this->assertEquals('photo_copy.png', $result);

        unlink(__DIR__ . DIRECTORY_SEPARATOR . 'tmp/photo.png');
    }

}