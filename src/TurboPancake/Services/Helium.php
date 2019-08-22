<?php
namespace TurboPancake\Services;

use Intervention\Image\ImageManager;
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
    protected $formats = [];

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $file, ?string $oldFile = null): ?string
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return null;
        }

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

         $this->generateImageFromFormats($targetPath);

        return pathinfo($targetPath, PATHINFO_BASENAME);
    }

    public function delete(string $filename): void
    {
        $targetPath = $this->path . DS . $filename;
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        $keys = array_keys($this->formats);
        foreach ($keys as $key) {
            $pathInfos = pathinfo($targetPath);
            $tmpTargetPath = $this->suffixPath($targetPath, $key);
            if (file_exists($tmpTargetPath)) {
                unlink($tmpTargetPath);
            }
        }
    }

    protected function generateImageFromFormats(string $sourcePath): void
    {
        foreach ($this->formats as $format => $properties) {
            $pathInfos = pathinfo($sourcePath);
            $targetPath = $this->suffixPath($sourcePath, $format);
            $targetPath = str_replace('.' . $pathInfos['extension'], '.png', $targetPath);

            $manager = new ImageManager(['driver' => 'imagick']);
            $image = $manager->make($sourcePath);

            if (isset($properties['resize'])) {
                [$width, $heigth, $fit] = $properties['resize'];
                if ($fit) {
                    $image->fit($width, $heigth);
                } else {
                    $image->resize($width, $heigth);
                }
            }

            $image->save($targetPath, 100, 'png');
        }
    }

    private function getPath(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            $pathInfos = pathinfo($targetPath);
            if (strlen($pathInfos['filename']) <= 200) {
                $targetPath = $this->suffixPath($targetPath, 'copy');
            } else {
                $targetPath = $this->suffixPath($targetPath, md5(time() . rand()));
            }
            $targetPath = $this->getPath($targetPath);
        }
        return $targetPath;
    }

    private function suffixPath(string $path, string $suffix): string
    {
        $pathInfos = pathinfo($path);
        return $pathInfos['dirname'] . self::DS .
               $pathInfos['filename'] . '_' . $suffix . '.' . $pathInfos['extension'];
    }

}
