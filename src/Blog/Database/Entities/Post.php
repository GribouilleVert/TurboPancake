<?php
namespace TurboModule\Blog\Database\Entities;

use TypeError;

class Post {

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $image;

    /**
     * @var int
     */
    public $categoryId;

    /**
     * @var string
     */
    public $categoryName;

    /**
     * @var string
     */
    public $content;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime
     */
    public $updatedAt;

    /**
     * @param \DateTime|string $createdAt
     * @throws \Exception
     */
    public function setCreatedAt($createdAt): void
    {
        if (is_string($createdAt)) {
            $this->createdAt = new \DateTime($createdAt);
        } elseif ($createdAt instanceof  \DateTime) {
            $this->createdAt = $createdAt;
        } else {
            throw new TypeError('Unexpected type ' . gettype($createdAt) . ' for parameter $createdAt');
        }
    }

    /**
     * @param \DateTime|string $updatedAt
     * @throws \Exception
     */
    public function setUpdatedAt($updatedAt): void
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new \DateTime($updatedAt);
        } elseif ($updatedAt instanceof  \DateTime) {
            $this->updatedAt = $updatedAt;
        } else {
            throw new TypeError('Unexpected type ' . gettype($updatedAt) . ' for parameter $createdAt');
        }
    }

    public function getThumbnail(): string
    {
        $pathInfos = pathinfo($this->image);
        $targetPath = $pathInfos['filename'] . '_thumbnail.png';
        return '/uploads/thumbnails/' . $targetPath;
    }

}
