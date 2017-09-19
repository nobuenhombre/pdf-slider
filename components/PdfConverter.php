<?php

namespace app\components;

use Imagick;

class PdfConverter {

    public
        $file_name,
        $slider_folder,
        $pages_count;

    private
        $imagick;

    public function __construct(string $file_name)
    {
        $this->file_name = $file_name;

    }

    public function convert()
    {
        $this->imagick = new Imagick($this->file_name);
        $this->pages_count = $this->imagick->getNumberImages();
        $this->imagick->setImageBackgroundColor('white');
        $this->imagick->setResolution(300,300);
        $this->imagick->setImageCompressionQuality(100);
        for($page_num=0; $page_num<$this->pages_count; $page_num++)
        {
            $this->imagick->setIteratorIndex($page_num);
            $this->imagick->writeImage(__DIR__ . '/../data/uploads/'."page-{$page_num}.jpg");
        }
        $this->imagick->destroy();
    }
}