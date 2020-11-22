<?php
/* @var $searchModel FamilySearch */

use common\models\FamilyUser;
use frontend\models\FamilySearch;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Семьи');

$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['_id'];
        }
    ],
    [
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => '',
        'value' => function () {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model) {
            // TODO перенести в контроллер
            $channels = FamilyUser::find()->where(['objectUuid' => $model->uuid])->all();
            return Yii::$app->controller->renderPartial('channels-details', ['model' => $model,
                'channels' => $channels]);
        },
        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data['title'];
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'width' => '100px',
        'header' => Yii::t('app', 'Действия'),
        'buttons' => [
        ],
        'template' => '{delete}'
    ]
];

echo GridView::widget([
    'id' => 'object-table-index',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a(Yii::t('app', 'Новый'),
                ['/family/new'],
                [
                    'class' => 'btn btn-success',
                    'title' => Yii::t('app', 'Новое'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modalAdd'
                ])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'id' => 'ww',
        'target' => GridView::TARGET_BLANK,
        'filename' => 'objects'
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="fa fa-house"></i>&nbsp;' . Yii::t('app', 'Семьи')
    ],
]);

$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

<div class="modal remote fade" id="modalAdd">
    <div class="modal-dialog" style="width: 800px; height: 400px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
        </div>
    </div>
</div>
