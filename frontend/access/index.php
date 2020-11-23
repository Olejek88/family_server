<?php

use frontend\controllers\AccessController;
use frontend\models\AccessModel;
use frontend\models\AccessSearch;
use kartik\grid\GridView;
use kartik\popover\PopoverX;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $searchModel AccessSearch */

?>

<?php
echo '<h1>' . Yii::t('app', 'Настройка прав доступа к разделам') . '</h1>';
$this->title = Yii::t('app', 'Настройка прав доступа');

$editableOptions = function ($model) {
    return [
        'inputType' => kartik\editable\Editable::INPUT_CHECKBOX,
        'preHeader' => Yii::t('app', 'Роль '),
        'formOptions' => [
            'action' => ['/access/update']
        ],
        'options' => [
            'label' => Yii::t('app', 'разрешение на ') . AccessController::getCoolText($model->permission) . ' ' . $model->model,
        ],
        'additionalData' => [
            'model' => $model->model,
            'permission' => $model->permission,

        ],
        'placement' => PopoverX::ALIGN_LEFT,
    ];
};

$editableOptionsDescription = function ($model) {
    return [
        'formOptions' => [
            'action' => ['/access/update'],
        ],
        'additionalData' => [
            'model' => $model->model,
            'permission' => $model->permission,
        ],
        'placement' => PopoverX::ALIGN_LEFT,
    ];
};


try {
    echo GridView::widget([
            'dataProvider' => $dataProvider,
            'pjax' => true,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'visible' => false,
                ],
                [
                    'class' => 'kartik\grid\DataColumn',
                    'attribute' => 'model',
                    'header' => Yii::t('app', 'Раздел'),
                    'group' => true,
                ],
                [
                    'class' => 'kartik\grid\DataColumn',
                    'attribute' => 'permission',
                    'header' => Yii::t('app', 'Разрешения'),
                ],
                [
                    'class' => 'kartik\grid\EditableColumn',
                    'attribute' => 'description',
                    'header' => Yii::t('app', 'Описание'),
                    'value' => function ($model) {
                        /* @var AccessModel $model */
                        $description = $model->permission . $model->model;
                        $description_table = $model->getDescription($description);
                        //Если пустое показываем по имени
                        if (is_null($description_table))
                            $description_table = $model->getNameDescription($description);
                        return $description_table;
                    },
                    'editableOptions' => $editableOptionsDescription,
                ],
                [
                    'class' => 'kartik\grid\EditableColumn',
                    'attribute' => 'admin',
                    'format' => 'html',
                    'header' => Yii::t('app', 'Администратор'),
                    'value' => function ($model) {
                        /* @var AccessModel $model */
                        return $model->getValue($model, 'admin');
                    },
                    'editableOptions' => $editableOptions,
                ],
                [
                    'class' => 'kartik\grid\EditableColumn',
                    'attribute' => 'user',
                    'header' => Yii::t('app', 'Пользователь'),
                    'format' => 'html',
                    'value' => function ($model) {
                        /* @var AccessModel $model */
                        return $model->getValue($model, 'user');
                    },
                    'editableOptions' => $editableOptions,
                ],
            ],
        ]
    );

} catch (Exception $e) {
}
?>
