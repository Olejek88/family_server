<?php

namespace api\controllers;

use common\components\MyHelpers;
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
            $success = true;
            $saved = array();
            $params = Yii::$app->request->bodyParams;
            foreach ($params as $item) {
                $model = User::findOne(['id' => $item['id'], 'email' => $item['email']]);
                if ($model == null) {
                    $model = new User();
                    $model->setAttribute('email', $item['email']);
                    $model->setAttribute('username', $item['username']);
                    $model->password_hash = Yii::$app->security->generatePasswordHash($item['password']);
                    $model->auth_key = Yii::$app->security->generateRandomString();
                    $model->setAttribute('password', $item['username']);
                    $model->setAttribute('status', User::STATUS_ACTIVE);
                    $model->setAttribute('createdAt', MyHelpers::parseFormatDate($item['createdAt']));
                    $model->setAttribute('changedAt', MyHelpers::parseFormatDate($item['changedAt']));
                }
                $model->setAttribute('username', $item['username']);
                if ($model->validate()) {
                    if ($model->save(false)) {
                        // запись для загружаемого файла
                        $file = Yii::$app->request->getBodyParam('file');
                        if ($file != null) {
                            if ($model->load($file, '')) {
                                $dir = Yii::getAlias('@frontend/web/');
                                $dir .= $model->getImageDir() . '/' . date('Y-m-d', strtotime($model['createdAt']));
                                if (!is_dir($dir)) {
                                    mkdir($dir, 0755, true);
                                }
                                // Для последующей валидации используем атрибут модели для загрузки файла
                                $model->upload = UploadedFile::getInstance($model, 'upload');
                                if ($model->upload == null) {
                                    return null;
                                }
                                $destination = $dir . '/' . $model['id'];
                                $fileMoved = $model->upload->saveAs($destination);
                                if (!$fileMoved) {
                                    return null;
                                }
                                $model->image = $destination;
                                if (!$model->save(false)) {
                                    return null;
                                }
                            }
                        }
                    } else {
                        $success = false;
                    }
                } else {
                    $success = false;
                }
            }
            $answer[] = 0;
            if ($success) {
                $answer["status_code"] = 0;
                $answer["message"] = "user successfully saved";
            } else {
                $answer["status_code"] = -1;
                $answer["message"] = "error";
            }
            return json_encode($answer);
        } else {
            throw new NotAcceptableHttpException();
        }
    }
}
