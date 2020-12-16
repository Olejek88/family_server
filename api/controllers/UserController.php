<?php

namespace api\controllers;

use common\models\User;
use Yii;
use yii\base\Controller;
use yii\base\Exception;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * @package api\controllers
 */
class UserController extends Controller
{
    public $modelClass = 'common\models\User';

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
        $query = User::find();

        $email = $req->getQueryParam('email');
        if ($email != null) {
            $query->andWhere(['email' => $email]);
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
     * @throws Exception
     */
    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->bodyParams;
            foreach ($params as $item) {
                $model = User::findOne(['id' => $item['_id']]);
                if ($model == null) {
                    $answer["status_code"] = -1;
                    $answer["message"] = "no user found";
                    return $answer;
                }
                $model->setAttribute('username', $item['username']);
                if (isset($item['last_latitude'])) {
                    $model->setAttribute('last_latitude', $item['last_latitude']);
                    $model->setAttribute('last_longitude', $item['last_longitude']);
                    $model->setAttribute('location', $item['location']);
                }

                if ($model->validate()) {
                    if ($model->save(false)) {
                        $answer["status_code"] = 0;
                        $answer["message"] = "user successfully saved";
                        return $answer;
                    }
                }
            }
            $answer["status_code"] = -1;
            $answer["message"] = "error";
            return json_encode($answer);
        } else {
            throw new NotAcceptableHttpException();
        }
    }

    /**
     * Метод для загрузки/сохранения атрибутов созданных/изменённых на мобильном клиенте
     *
     * @return array
     */
    public function actionImage()
    {
        if (Yii::$app->request->isPost) {
            $imageFile = UploadedFile::getInstanceByName('image');
            $params = Yii::$app->request->bodyParams;
            $model = User::findOne(['id' => $params['userId']]);
            $dir = Yii::getAlias('@frontend/web/storage/users');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $destination = $dir . '/' . $imageFile->name;
            $imageFile->saveAs($destination);
            $model->image = $imageFile->name;
            if (!$model->save(false)) {
                return null;
            }
            $answer["status_code"] = 0;
            $answer["message"] = "user successfully saved";
            return $answer;
        }
        $answer["status_code"] = -1;
        $answer["message"] = "error";
        return $answer;
    }
}
