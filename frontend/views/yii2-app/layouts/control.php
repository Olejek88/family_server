<?php

use common\models\Register;
use common\models\Settings;
use yii\helpers\Html;
use yii\widgets\Pjax;

$registers = Register::find()->orderBy('createdAt DESC')->limit(7)->all();
$settings = Settings::find()->all();
?>
<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-references-tab" data-toggle="tab"><i class="fa fa-book"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane" id="control-sidebar-stats-tab"><?php echo Yii::t('app', 'Настройки') ?></div>
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <?php Pjax::begin(['id' => 'options']); ?>
            <?= Html::beginForm(['../site/config'], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>
            <h4 class="control-sidebar-heading"><?php echo Yii::t('app', 'Основные настройки') ?></h4>
            <?= Html::hiddenInput('url', Yii::$app->request->getUrl(), ['id' => 'url',]); ?>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                </label>
            </div>
            <button type="submit"
                    class="btn btn-info btn-sm"><?php echo Yii::t('app', 'сохранить настройки') ?></button>
            <?php
            echo Html::endForm();
            Pjax::end();
            ?>
        </div>
        <div class="tab-pane" id="control-sidebar-stats-tab"><?php echo Yii::t('app', 'Настройки') ?></div>
    </div>
</aside>
<div class="control-sidebar-bg"></div>
