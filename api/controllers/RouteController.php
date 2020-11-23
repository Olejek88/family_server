<?php

namespace api\controllers;

use common\models\Route;
use common\models\Token;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class RouteController extends ActiveController
{
    public $modelClass = 'common\models\Route';

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
        unset($actions['create']);
        return $actions;
    }

    /**
     * Index
     *
     * @return Route[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $online = [];
        $offline = [];
        $gps = 0;

        $userGet = Token::find()
            ->select('id, issued')
            ->where('issued >= CURDATE()')
            ->all();

        foreach ($userGet as $keys => $val) {
            $users[] = User::find()
                ->select('uuid')
                ->where(['id' => $val['id']])
                ->one();

            $userList[] = $users[$keys];

            $today = time();
            $userUnix[] = strtotime($userList[$keys]->connectionDate);
            $threshold = $today - 300;

            if ($userUnix[$keys] >= $threshold) {
                if (isset($online) && is_array($online)) {
                    $online[] = $userList[$keys]->uuid;
                }
            } else {
                if (isset($offline) && is_array($offline)) {
                    $offline[] = $userList[$keys]->uuid;
                }
            }

            if (count($userList) >= 1) {
                $list = count($userList) - 1;
                $gps = Route::find()
                    ->select('userId, latitude, longitude, date')
                    ->where('date >= CURDATE()')
                    ->asArray()
                    ->all();
            }
        }

        $result = $gps;
        return $result;
    }

    /**
     * Action create
     *
     * @return array
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = true;
        $saved = array();
        $rawData = Yii::$app->getRequest()->getRawBody();
        $items = json_decode($rawData, true);
        foreach ($items as $item) {
            $line = new Route();
            $line->setAttributes($item);
            $line->userId = $item['userId'];
            try {
                if ($line->save()) {
                    $saved[] = [
                        '_id' => $item['_id'],
                        'uuid' => $item['userId'],
                    ];
                } else {
                    $success = false;
                }
            } catch (Exception $e) {
                $saved[] = [
                    '_id' => $item['_id'],
                    'uuid' => $item['userId'],
                ];
            }
        }

        return ['success' => $success, 'data' => $saved];
    }
}
