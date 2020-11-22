<?php

namespace frontend\controllers;


use common\models\Event;
use common\models\Objects;
use common\models\ServiceRegister;
use common\models\User;
use Yii;
use yii\web\NotFoundHttpException;


$accountUser = Yii::$app->user->identity;
$currentUser = User::findOne(['id' => $accountUser['id']]);
if ($currentUser == null) {
    /** @noinspection PhpUnhandledExceptionInspection */
    throw new NotFoundHttpException(Yii::t('app', 'Пользователь не найден!'));
}

Yii::$app->view->params['currentUser'] = $currentUser;

$userImage = $currentUser->getImageUrl();
if (!$userImage)
    $userImage = Yii::$app->request->baseUrl . '/images/unknown2.png';
$userImage = str_replace("storage", "files", $userImage);

Yii::$app->view->params['userImage'] = $userImage;

