<?php
/*
 */
$this->title = Yii::t('app', 'Сводная');
$this->registerJsFile('/js/HighCharts/highcharts.js');
$this->registerJsFile('/js/HighCharts/modules/exporting.js');

?>

<!-- Info boxes -->
<div class="row">
</div>

<div class="row">
    <div class="col-md-7">
    </div>
    <!-- /.col -->
    <div class="col-md-5">
    </div>
</div>
<!-- /.row -->

<footer class="main-footer" style="margin-left: 0 !important;">
    <?= $this->render('footer'); ?>
</footer>
