<?php

namespace app\components;

use yii\helpers\VarDumper;
use Imagick;
use ZipArchive;

class FileWorker {

    const
        FILE_COPY_PARTS=1,
        FILE_COPY_SAFE=2;

    public static function copy_from_url(string $url, int $method = FileWorker::FILE_COPY_PARTS) : array {
        $result = array();
        $result['status'] = true;
        $result['msg'] = 'Ok';



                if ($method == FileWorker::FILE_COPY_PARTS) {
                    $handleLOC = fopen($local, "wb");
                    $handleURL = fopen($url, "rb");
                    while (!feof($handleURL)) {
                        $contentURL = '';
                        $contentURL = fread($handleURL, 8192);
                        if (fwrite($handleLOC, $contentURL) === FALSE) {
                            $result['status'] = false;
                            $result['msg'] = "Can't write file";
                            break;
                        }
                        unset($contentURL);
                    }
                    fclose($handleURL);
                    fclose($handleLOC);
                } else {
                    $contentURL = @file_get_contents($url);
                    static::save($local, $contentURL);
                }


        return $result;
    }
}