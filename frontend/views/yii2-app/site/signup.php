<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model SignupForm */

use frontend\models\SignupForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'SignUp');

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="wrapper-block">
    <div class="panel panel-default wrapper-login-panel">
        <div class="panel-body">
            <div class="login-box">
                <div class="login-logo text-center">
                    <h4>
                        <a href="/" style=" color: #333; text-decoration: none;">
                            <b><?php echo Yii::t('app', 'Family Finder') ?></b>
                        </a>
                    </h4>
                </div>

                <div class="login-box-body">
                    <!-- <p class="login-box-msg">Введите учетные данные</p> -->

                    <?php $form = ActiveForm::begin(['id' => 'form-signup', 'enableClientValidation' => false]); ?>

                    <?= $form->field($model, 'username', $fieldOptions1)->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'email', $fieldOptions1) ?>

                    <?= $form->field($model, 'password', $fieldOptions2)->passwordInput() ?>

                    <div class="row">
                        <div class="col-xs-4">
                            <?= Html::submitButton(Yii::t('app', 'Sign Up'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>

            </div>
        </div>
    </div>
</div>