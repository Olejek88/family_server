<?php

namespace api\controllers;

use common\models\Crash;
use common\models\User;
use Yii;
use yii\base\Controller;
use yii\web\Response;

/**
 * Class CrashController
 * @package api\controllers
 */
class CrashController extends Controller
{

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
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
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // проверяем параметры запроса
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->bodyParams;
            $crash = new Crash();
            /** @var User $user */
            $user = null;
            if (isset($_GET['token'])) {
                $token = $_GET['token'];
                $user = TokenController::getUserByToken($token);
                if ($user) {
                    $crash['userUuid'] = $user['uuid'];
                }
            }
            if (isset($params['REPORT_ID'])) {
                $crash['report_id'] = $params['REPORT_ID'];
            }
            if (isset($params['APP_VERSION_CODE'])) {
                $crash['app_version_code'] = "v" . $params['APP_VERSION_CODE'];
            }
            if (isset($params['APP_VERSION_NAME'])) {
                $crash['app_version_name'] = $params['APP_VERSION_NAME'];
            }
            if (isset($params['PHONE_MODEL'])) {
                $crash['phone_model'] = $params['PHONE_MODEL'];
            }
            if (isset($params['BRAND'])) {
                $crash['brand'] = $params['BRAND'];
            }
            if (isset($params['PRODUCT'])) {
                $crash['product'] = $params['PRODUCT'];
            }
            if (isset($params['ANDROID_VERSION'])) {
                $crash['android_version'] = $params['ANDROID_VERSION'];
            }
            if (isset($params['STACK_TRACE'])) {
                $crash['stack_trace'] = $params['STACK_TRACE'];
            }
            if (isset($params['USER_APP_START_DATE'])) {
                $crash['user_app_start_date'] = $params['USER_APP_START_DATE'];
            }
            if (isset($params['USER_CRASH_DATE'])) {
                $crash['user_crash_date'] = $params['USER_CRASH_DATE'];
            }
            if (isset($params['LOGCAT'])) {
                $crash['logcat'] = $params['LOGCAT'];
            }
            $crash->save();
            return ['success' => true];
        }
    }
}
