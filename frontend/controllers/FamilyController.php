<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\Family;
use common\models\FamilyUser;
use common\models\User;
use frontend\models\FamilySearch;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

class FamilyController extends ParentController
{
    protected $modelClass = Family::class;

    /**
     * Return average coordinates around all objects
     * @param User $user1
     * @param User $user2
     * @return array
     */
    public static function getDistance($user1, $user2)
    {
        return sqrt($user1->last_latitude * $user1->last_latitude + $user2->last_longitude * $user2->last_longitude);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Object models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Family::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Family'][$_POST['editableIndex']]['title'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }

        $searchModel = new FamilySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 200;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Deletes an existing Object model.
     * If deletion is successful, the browser will be redirected to the 'table' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $object = $this->findModel($id);
        if ($object) {
            $object->deleted = true;
            $object->save();
        }
        return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH) . '?node=' . $object['_id'] . 'k');
    }

    /**
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Family
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Family::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет отвязывание выбранного оборудования от пользователя
     * @return mixed
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDeleted()
    {
        $request = Yii::$app->request;
        $selected_node = $request->post('selected_node');
        $type = $request->post('type');

        if ($selected_node && $type) {
            $selected_node = rtrim($selected_node, 'k');
            if (is_numeric($selected_node)) {
                if ($type == 'user') {
                    $fuId = FamilyUser::find()->where(['_id' => $selected_node])
                        ->andWhere(['deleted' => 0])
                        ->limit(1)
                        ->one();
                    if ($fuId) {
                        $fuId->delete();
                        $return['code'] = 0;
                        $return['message'] = '';
                        return json_encode($return);
                    }
                } else {
                    $objectById = Family::find()->where(['_id' => $selected_node])
                        ->andWhere(['deleted' => 0])
                        ->limit(1)
                        ->one();
                    if ($objectById) {
                        $objectById['deleted'] = true;
                        $objectById->save();
                        $return['code'] = 0;
                        $return['message'] = '';
                        return json_encode($return);
                    }
                }
            }
        }
        $return['code'] = -1;
        $return['message'] = 'Неправильно заданы параметры';
        return json_encode($return);
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTree()
    {
        $search = (isset($_GET['sq']) && !empty($_GET['sq'])) ? $_GET['sq'] : null;

        $fullTree = array();
        $families = Family::find()
            ->andWhere(['deleted' => 0])
            ->orderBy('title')
            ->all();
        foreach ($families as $family) {
            $fullTree['children'][] = [
                'title' => $family['title'],
                'key' => $family['_id'],
                'type' => 'family',
                'expanded' => true,
                'folder' => true
            ];
            /** @var FamilyUser[] $users */
            $users = FamilyUser::find()
                ->where(['familyUuid' => $family['uuid']])
                ->all();
            foreach ($users as $user) {
                $childIdx = count($fullTree['children']) - 1;
                if ($user->user->status == 10) {
                    $links = Html::a('<span class="fa fa-th"></span>&nbsp',
                        ['/user/map', 'id' => $user->user['id']]
                    );
                    $fullTree['children'][$childIdx]['children'][] = [
                        'title' => $user->user['username'],
                        'key' => $user->user['id'],
                        'type' => 'user',
                        'links' => $links,
                        'latitude' => $user->user['last_latitude'],
                        'longitude' => $user->user['last_longitude'],
                        'email' => $user->user['email'],
                        'expanded' => true,
                        'folder' => false
                    ];
                }
            }
        }

        $users = User::find()
            ->orderBy('username')
            ->all();
        foreach ($users as $user) {
            $fullTree['children'][] = [
                'title' => $user['username'],
                'latitude' => $user['last_latitude'],
                'longitude' => $user['last_longitude'],
                'email' => $user['email'],
                'key' => $user['id'],
                'type' => 'user',
                'links' => "",
                'expanded' => true,
                'folder' => false
            ];
        }

        return $this->render(
            'tree',
            [
                'objects' => $fullTree,
                'sq' => $search
            ]
        );
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        if (!empty($_POST['selected_node'])) {
            /** @var Family $currentObject */
            $currentFamily = Family::find()->where(['_id' => $_POST['selected_node']])->one();
            if ($currentFamily) {
                $object_uuid = $currentFamily['uuid'];
                $user = new User();
                return $this->renderAjax('../user/_edit_users', [
                    'model' => $user,
                    'family_uuid' => $object_uuid
                ]);
            }
        } else {
            $family = new Family();
            return $this->renderAjax('../family/_add_form', [
                'model' => $family
            ]);
        }
        return null;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет редактирование
     *
     * @return mixed
     */
    public
    function actionEdit()
    {
        if (!empty($_POST['selected_node'])) {
            /** @var Family $currentObject */
            $currentFamily = Family::find()->where(['_id' => $_POST['selected_node']])->one();
            if ($currentFamily) {
                $object_uuid = $currentFamily['uuid'];
                $user = new User();
                return $this->renderAjax('../family/_add_user', [
                    'model' => $user,
                    'object_uuid' => $object_uuid
                ]);
            }
        }
        return null;
    }

    /**
     * Creates a new Object model.
     * @return mixed
     */
    public
    function actionSave()
    {
        if (isset($_POST['familyUuid']))
            $model = Family::find()->where(['uuid' => $_POST['objectUuid']])->limit(1)->one();
        else
            $model = new Family();
        if ($model->load(Yii::$app->request->post())) {
            $model->deleted = 0;
            if ($model->save(false)) {
                return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH) . '?node=' . $model['_id'] . 'k');
            } else {
                $return['code'] = -1;
                $return['message'] = json_encode($model->errors);
                return json_encode($return);
            }
        }
        return false;
    }

    /**
     * Restore an existing Objects model.
     *
     * @return mixed
     */
    public
    function actionRestore()
    {
        if (isset($_GET['uuid'])) {
            $object = Family::find()->where(['uuid' => $_GET['uuid']])->one();
            if ($object) {
                $object['deleted'] = false;
                $object['changedAt'] = date("Y-m-d H:i:s");
                $object->save();
            }
        }
        return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH));
    }

    /**
     * @return mixed
     */
    public
    function actionDashboard()
    {
        return null;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public
    function actionUserCopy()
    {
        if (isset($_POST["from"]) && isset($_POST["to"])) {
            /** @var Family $family */
            $family = null;
            if (isset($_POST["to"]))
                $family = Family::find()->where(['_id' => $_POST["to"]])->one();
            /** @var User $user */
            $user = User::find()->where(['email' => $_POST["from"]])->one();

            if ($user && $family) {
                $familyUser = FamilyUser::find()
                    ->where(['familyUuid' => $family->uuid])
                    ->andWhere(['userId' => $user->id])
                    ->one();
                if (!$familyUser) {
                    $newFamilyUser = new FamilyUser();
                    $newFamilyUser->uuid = MainFunctions::GUID();
                    $newFamilyUser->userId = $user->id;
                    $newFamilyUser->familyUuid = $family->uuid;
                    $newFamilyUser->createdAt = date("Y-m-d H:i:s");
                    $newFamilyUser->changedAt = date("Y-m-d H:i:s");
                    $newFamilyUser->save();
                    if ($newFamilyUser->_id) {
                        $return['code'] = 0;
                        $return['message']['_id'] = $user->id;
                        $return['message']['data'] = $user;
                        $return['message']['title'] = $user->username;
                        return json_encode($return);
                    }
                } else {
                    $return['code'] = -1;
                    $return['message'] = 'User already present';
                    return json_encode($return);
                }
                $return['code'] = -1;
                $return['message'] = 'Не удалось создать пользователя' . json_encode($newFamilyUser->errors);
                return json_encode($return);
            }
        }
        $return['code'] = -1;
        $return['message'] = 'Неправильно заданы параметры';
        return json_encode($return);
    }
}
