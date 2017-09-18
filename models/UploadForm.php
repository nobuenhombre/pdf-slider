<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public
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
            $this->pdfFile->saveAs(__DIR__ . '/../data/uploads/' . $this->pdfFile->baseName . '.' . $this->pdfFile->extension);
            return true;
        } else {
            return false;
        }
    }
}