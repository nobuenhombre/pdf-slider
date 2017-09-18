<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Please Upload File :: '.APP_NAME;
?>
<div class="site-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <?= $form->field($model, 'pdfFile')->fileInput() ?>
    <?= Html::submitButton('Конвертировать', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
</div>
