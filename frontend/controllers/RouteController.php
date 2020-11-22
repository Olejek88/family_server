<?php

namespace frontend\controllers;

use common\models\Route;
use common\models\User;
use frontend\models\RouteSearch;
use Yii;

class RouteController extends ParentController
{
    protected $modelClass = Route::class;

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        $users = User::find()->orderBy('username')->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'users' => $users
        ]);
    }
}
