<?php

namespace app\components;

use yii\helpers\VarDumper;
use Imagick;

class PdfConverter {

    public
        $id,
        $file_name,
        $slider_folder,
        $pages_count;

    private
        $imagick;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->file_name = SliderStore::get_source($this->id);
    }

    public function get_directory(string $id)
    {
        $dir = __DIR__ . "/../web/images/{$id}/";
        @mkdir($dir, 0777, true);
        return $dir;
    }

    public function convert()
    {
        try {
            SliderStore::set_status($this->id, SliderStore::STATUS_IN_PROGRESS);

            \Yii::trace(VarDumper::dumpAsString($this->file_name), 'PdfConverter->convert()');

            $this->imagick = new Imagick($this->file_name);
            $this->pages_count = $this->imagick->getNumberImages();

            SliderStore::create($this->id, $this->pages_count);

            $this->imagick->setImageBackgroundColor('white');
            $this->imagick->setResolution(300, 300);
            $this->imagick->setImageCompressionQuality(100);
            for ($page_num = 0; $page_num < $this->pages_count; $page_num++) {
                $this->imagick->setIteratorIndex($page_num);
                $dir = $this->get_directory($this->id);
                $this->imagick->writeImage("{$dir}page-{$page_num}.jpg");

                SliderStore::set_progress($this->id, $this->pages_count, $page_num);
            }
            $this->imagick->destroy();

            SliderStore::set_status($this->id, SliderStore::STATUS_SUCCESS);
        } catch (\Exception $exception) {
            SliderStore::set_status($this->id, SliderStore::STATUS_ERROR);
        }
    }
}