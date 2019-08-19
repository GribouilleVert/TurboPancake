<?php
namespace TurboPancake\Services;

use Psr\Http\Message\UploadedFileInterface;

class Helium {

    private const DS = DIRECTORY_SEPARATOR;

    /**
     * @var string Chemin vers lequel les fichiers seronts envoyés
     */
    protected $path;

    /**
     * @var array Liste des actions de formatages sur les images
     */
    protected $formats;

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $file, ?string $oldFile = null): string
    {
        if ($oldFile) {
            $this->delete($oldFile);
        }
        $filename = $this->path . DS . $file->getClientFilename();
        $targetPath = $this->getPath($filename);

        $directoryName = dirname($targetPath);
        if (!file_exists($directoryName)) {
            mkdir($directoryName, 0777, true);
        }

        $file->moveTo($targetPath);
        return pathinfo($targetPath, PATHINFO_BASENAME);
    }

    public function delete(string $filename): void
    {
        $targetPath = $this->path . DS . $filename;
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
    }

    private function getPath(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            $pathInfos = pathinfo($targetPath);
            $targetPath = $pathInfos['dirname'] . DS . $pathInfos['filename'] . '_copy.' . $pathInfos['extension'];
            $targetPath = $this->getPath($targetPath);
        }
        return $targetPath;
    }

}
