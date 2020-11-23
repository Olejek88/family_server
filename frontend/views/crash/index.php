<?php

use common\models\User;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = 'Crash';

$gridColumns = [
    [
        'attribute' => 'createdAt',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'userId',
        'content' => function ($data) {
            if ($data['userId'] != null)
                return $data['user']['username'];
            return '-';
        },
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(User::find()->orderBy('username')->all(),
            'uuid', 'name'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Anybody')],
    ],
    [
        'attribute' => 'report_id',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'app_version_name',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'phone_model',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['brand'] . ' ' . $data['phone_model'];
        },
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'android_version',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'user_app_start_date',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'content' => function ($data) {
            return date("Y-m-d H:i:s", strtotime($data['user_app_start_date']));
        },
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'user_crash_date',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'content' => function ($data) {
            return date("Y-m-d H:i:s", strtotime($data['user_crash_date']));
        },
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'stack_trace',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return Html::a('<span class="fa fa-list"></span> stack',
                ['../crash/stack', 'id' => $data->_id],
                [
                    'title' => Yii::t('app', 'Стек'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modalStack',
                ]);
        },
    ]
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [],
    'pjax' => false,
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
        'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; ' . Yii::t('app', 'App errors'),

    ],
]);
$this->registerJs('$("#modalStack").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

<div class="modal remote fade" id="modalStack">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
