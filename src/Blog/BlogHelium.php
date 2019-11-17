<?php
namespace TurboModule\Blog;

use TurboPancake\Services\Helium;

class BlogHelium extends Helium {

    protected $path = 'public/uploads/thumbnails';

    protected $formats = [
        'thumbnail' => [
            'resize' => [320, 200, true]
        ]
    ];
}
