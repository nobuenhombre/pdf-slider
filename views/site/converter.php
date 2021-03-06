<?php

/**
 * @var $this yii\web\View
 * @var $model app\models\UploadForm
 */

use yii\bootstrap\Progress;

$this->title = 'Convert File :: '.APP_NAME;
?>
<div class="site-index">
    <?= Progress::widget([
        'percent' => 0,
        'options' => ['id'=>'ProgressBar','data-id' => $model->id]
    ]);
    ?>
    <div id="ProgressLabel" class="my-icon my-icon-loading"></div>
    <div id="MessagesConsole"></div>
</div>
