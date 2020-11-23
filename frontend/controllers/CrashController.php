<?php

namespace frontend\controllers;

use common\models\Crash;
use frontend\models\CrashSearch;
use Yii;

class CrashController extends FamilyController
{
    protected $modelClass = Crash::class;

    /**
     * Lists all Crash models.
     * @return mixed
     * @throws \Exception
     */
    public function actionIndex()
    {
        $searchModel = new CrashSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionStack($id)
    {
        $stack = "";
        /** @var Crash $crash */
        $crash = Crash::find()
            ->where(['_id' => $id])->one();
        if ($crash) {
            $stack = $crash->stack_trace;
        }
        return $this->renderAjax('_stack', [
            'stack' => $stack
        ]);
    }
}
