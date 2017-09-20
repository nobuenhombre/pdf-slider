<?php

/**
 * @var $this yii\web\View
 * @var $id string
 * @var $qty int
 */

$this->title = 'Slider :: '.APP_NAME;
?>
<div class="site-index">
    <a href="http://pdf-slider/zips/<?=$id?>.zip">Download Link</a>
    <ul class="rslides" id="slider">
        <?php
            for($i=0; $i<$qty; $i++) {
                ?>
                <li><a href="#"><img src="/images/<?=$id?>/page-<?=$i?>.jpg" alt=""></a></li>
                <?php
            }
        ?>
    </ul>
</div>
