<?php

/* @var $leafLet */

/* @var $title */

use dosamigos\leaflet\widgets\Map;

$this->title = Yii::t('app', 'Карта');
if (isset($title))
    $this->title = $title;
$this->registerJs('$(window).on("resize", function () { $("#w0").height($(window).height()-50); $("#w0").width($(window).width()-50); }).trigger("resize");');
?>

<div class="box-relative" style="width: 100%">
    <?php
    echo Map::widget(['leafLet' => $leafLet]);
    ?>
    <div id="polygon-control"
         style="width: 200px; height: 290px; margin:15px; z-index: 1000; position: absolute; top: 50px; right: 10px; display: none">
        <button id="enable-button"><i class="fa fa-arrows"></i></button>
        <button id="disable-button"><i class="fa fa-ban"></i></button>
        <button id="get-button-features-polygon"><i class="fa fa-save"></i></button>
    </div>
    <button id="show-save" style="width: 45px; height: 40px; z-index: 1000; position: absolute; top: 60px; right: 10px">
        <i class="fa fa-arrows"></i>
    </button>
</div>

<div class="modal remote fade" id="modalAdd">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
        </div>
    </div>
</div>
