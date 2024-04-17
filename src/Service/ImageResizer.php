<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageResizer
{
    public function resize($filePath, $width, $height)
    {
        $imagine = new Imagine();

        $image = $imagine->open($filePath);
        $image->resize(new Box($width, $height));

        $image->save($filePath);
    }
}