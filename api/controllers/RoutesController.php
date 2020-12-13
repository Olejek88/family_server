<?php

namespace api\controllers;

use common\models\Route;
use common\models\Token;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class RoutesController extends ActiveController
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
            //TODO temporary out (no token)
            //throw new UnauthorizedHttpException();
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
                ->select('id')
                ->where(['id' => $val['userId']])
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
     * @throws InvalidConfigException
     */
    public function actionSend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request->getBodyParams();
        $userId = $request['userId'];
        $items = $request['routes'];
        foreach ($items as $item) {
            $line = new Route();
            $date = strtotime($item['date']);
            $line->date = date("Y-m-d H:i:s", $date);
            $line->latitude = $item['latitude'];
            $line->longitude = $item['longitude'];
            $line->userId = $userId;
            try {
                $line->save();

            } catch (Exception $e) {
                $answer["status_code"] = -1;
                $answer["message"] = $e->getMessage();
                return $answer;
            }
        }
        $answer["status_code"] = 0;
        $answer["message"] = "successfully stored";
        return $answer;
    }
}
