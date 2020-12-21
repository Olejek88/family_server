<?php

namespace api\controllers;

use common\models\FamilyUser;
use common\models\User;
use Yii;
use yii\base\Controller;
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
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->bodyParams;
            $model = User::findOne(['email' => $params['userLogin']]);
            if ($model == null) {
                $answer["status_code"] = -1;
                $answer["message"] = "no user found";
                return $answer;
            }
            // TODO just one family
            $familyUser = FamilyUser::find()->where(['userId' => $model['id']])->one();
            if ($familyUser) {
                $familyUsers = FamilyUser::find()
                    ->where(['familyUuid' => $familyUser['familyUuid']])
                    ->andWhere(['!=', 'userId', $model['id']])
                    ->all();
                $users = array();
                foreach ($familyUsers as $familyUser) {
                    $users[] = $familyUser->user;
                }
                return $users;
            }
            $answer["status_code"] = 0;
            $answer["message"] = "no users in family";
            return $answer;
        }
        $answer["status_code"] = -1;
        $answer["message"] = "no post request";
        return $answer;
    }
}
