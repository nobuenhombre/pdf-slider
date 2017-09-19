<?php

namespace app\models;

use app\components\PdfConverter;
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
            $file_name = __DIR__ . '/../data/uploads/' . $this->pdfFile->baseName . '.' . $this->pdfFile->extension;
            $this->pdfFile->saveAs($file_name);
            $pdf_converter = new PdfConverter($file_name);
            $pdf_converter->convert();
            return true;
        } else {
            return false;
        }
    }
}