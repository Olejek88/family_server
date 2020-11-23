<?php

namespace api\controllers;

use common\components\MyHelpers;
use common\models\Family;
use Yii;
use yii\base\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * @package api\controllers
 */
class FamilyController extends Controller
{
    public $modelClass = 'common\models\Family';

    /**
     * Init
     *
     * @return void
     * @throws UnauthorizedHttpException
     */
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $token = TokenController::getTokenString(Yii::$app->request);
        // проверяем авторизацию пользователя
        if (!TokenController::isTokenValid($token)) {
            throw new UnauthorizedHttpException();
        }
    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * Displays homepage.
     *
     * @return array
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // проверяем параметры запроса
        $req = Yii::$app->request;
        $query = Family::find();

        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        // выбираем данные из базы
        $result = $query->all();
        return $result;
    }

    /**
     * Метод для загрузки/сохранения атрибутов созданных/изменённых на мобильном клиенте
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {
            $success = true;
            $saved = array();
            $params = Yii::$app->request->bodyParams;
            foreach ($params as $item) {
                $model = Family::findOne(['_id' => $item['_id'], 'uuid' => $item['uuid']]);
                if ($model == null) {
                    $model = new Family();
                }

                $model->attributes = $item;
                $model->setAttribute('_id', $item['_id']);
                $model->setAttribute('title', $item['title']);
                $model->setAttribute('createdAt', MyHelpers::parseFormatDate($item['createdAt']));
                $model->setAttribute('changedAt', MyHelpers::parseFormatDate($item['changedAt']));

                if ($model->validate()) {
                    if ($model->save(false)) {
                        $saved[] = [
                            '_id' => $item['_id'],
                            'uuid' => $item['uuid']
                        ];
                    } else {
                        $success = false;
                    }
                } else {
                    $success = false;
                }
            }
            return ['success' => $success, 'data' => $saved];
        } else {
            throw new NotAcceptableHttpException();
        }
    }
}
