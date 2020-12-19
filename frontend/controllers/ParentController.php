<?php

namespace frontend\controllers;

use common\models\FamilyModel;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class ParentController extends Controller
{
    protected $modelClass;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        return true;
        if (parent::beforeAction($action)) {
            $access = false;
            try {
                /* @var FamilyModel $modelClass */
                $modelClass = $this->modelClass;
                /* @var FamilyModel $model */
                $model = new $modelClass;
                $modelPermissions = $model->getPermissions();
                $permiss = null; //Проверка на массив/строку для описания прав
                if (isset($modelPermissions[$action->id])) {
                    if (is_array($modelPermissions[$action->id])) {
                        $permiss = $modelPermissions[$action->id]['name'];
                    } else {
                        $permiss = $modelPermissions[$action->id];
                    }

                    if (Yii::$app->user->can($permiss))
                        $access = true;
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage(), 'frontend/controllers/FamilyController.php');
            }

            if (!$access) {
                Yii::$app->session->setFlash('warning', '<h3>' .
                    Yii::t('app', 'Не достаточно прав доступа.') . '</h3>');
                $this->redirect('/');
            }

            return $access;
        } else {
            return false;
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function checkDelete($uuid)
    {
        return true;
    }
}