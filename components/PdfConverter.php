<?php

namespace app\components;

use yii\helpers\VarDumper;
use Imagick;
use ZipArchive;

class PdfConverter {

    public
        $id,
        $file_name,
        $slider_folder,
        $pages_count;

    private
        $imagick, $zip_archive;

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

            $this->zip_archive = new ZipArchive();
            if ($this->zip_archive->open(__DIR__ . "/../web/zips/{$this->id}.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

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

                    $this->zip_archive->addFile("{$dir}page-{$page_num}.jpg","/images/{$this->id}/page-{$page_num}.jpg");
                    SliderStore::set_progress($this->id, $this->pages_count, $page_num);
                }
                $this->imagick->destroy();

                $this->zip_archive->addFile(__DIR__ . "/../web/assets/slider/css/slider.css", "/assets/slider/css/slider.css");
                $this->zip_archive->addFile(__DIR__ . "/../web/assets/slider/js/slider.js", "/assets/slider/js/slider.js");
                $this->zip_archive->setArchiveComment("PDF to HTML Slider converter demo - {$this->id}");
                $this->zip_archive->addFromString('index.html', file_get_contents("http://pdf-slider/slider?id={$this->id}"));

            } else {
                SliderStore::set_status($this->id, SliderStore::STATUS_ERROR);
            }
            $this->zip_archive->close();

            SliderStore::set_status($this->id, SliderStore::STATUS_SUCCESS);
        } catch(\Exception $exception) {
           SliderStore::set_status($this->id, SliderStore::STATUS_ERROR);
        }
    }
}