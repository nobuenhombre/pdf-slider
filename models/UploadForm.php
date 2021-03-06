<?php

namespace app\models;

use app\components\SliderStore;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class UploadForm
 *
 * Модель загрузки файла PDF
 *
 * @package app\models
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public
        $id,
        $pdfFile,
        $success;

    public function rules()
    {
        return [
            [['pdfFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $file_name = __DIR__ . '/../data/uploads/' . $this->pdfFile->baseName . '.' . $this->pdfFile->extension;
            $this->pdfFile->saveAs($file_name);
            $this->id = md5_file($file_name);
            SliderStore::set_source($this->id, $file_name);
            return true;
        } else {
            return false;
        }
    }
}