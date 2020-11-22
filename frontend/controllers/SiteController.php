<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\ActionRegister;
use common\models\Alarm;
use common\models\DistrictCoordinates;
use common\models\Event;
use common\models\EventType;
use common\models\LoginForm;
use common\models\MeasureType;
use common\models\Objects;
use common\models\ObjectSubType;
use common\models\ObjectType;
use common\models\ParameterType;
use common\models\Register;
use common\models\ServiceRegister;
use common\models\User;
use dosamigos\leaflet\controls\Layers;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\LeafLet;
use dosamigos\leaflet\types\Icon;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\types\Point;
use Exception;
use frontend\models\EventSearch;
use frontend\models\SignupForm;
use koputo\leaflet\plugins\subgroup\Subgroup;
use koputo\leaflet\plugins\subgroup\SubgroupCluster;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class SiteController
 * @package frontend\controllers
 *
 * @property-read mixed $layers
 */
class SiteController extends Controller
{
    /**
     * Behaviors
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['signup', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'dashboard', 'error', 'timeline', 'config', 'stats', 'map'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['error']);
        return $actions;
    }

    /**
     * Displays homepage.
     *
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $layer = self::getLayers();
        $center = $layer['coordinates'];

        // The Tile Layer (very important)
        $tileLayer = new TileLayer([
            'urlTemplate' => 'https://api.tiles.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw',
            'clientOptions' => [
                'subdomains' => ['1', '2', '3', '4'],
            ],
        ]);
        $leaflet = new LeafLet([
            'center' => $center, // set the center
            'zoom' => 15
        ]);

        $layers = new Layers();

        // Different layers can be added to our map using the `addLayer` function.
        $leaflet->addLayer($tileLayer);

        $subGroupPlugin = new SubgroupCluster();
        //$subGroupPlugin->addSubGroup($layer['objectGroup']);
        $subGroupPlugin->addSubGroup($layer['regionGroup']);
        $subGroupPlugin->addSubGroup($layer['alarmGroup']);

        $subGroupPlugin->addSubGroup($layer['heatGroup']);
        $subGroupPlugin->addSubGroup($layer['waterGroup']);
        $subGroupPlugin->addSubGroup($layer['powerGroup']);
        $layers->setOverlays([]);

        $layers->setName('ctrlLayer');

        $leaflet->addControl($layers);
        $layers->position = 'bottomleft';

        // install to LeafLet component
        $leaflet->plugins->install($subGroupPlugin);

        return $this->render(
            'index',
            [
                'title' => 'Стартовая страница',
                'leafLet' => $leaflet,
            ]
        );
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public
    function getLayers()
    {
        $users = User::find()->where(['status' => User::STATUS_ACTIVE])->all();
        $userList[] = $users;
        $usersGroup = new SubGroup();
        $usersGroup->setTitle(Yii::t('app', 'Пользователи'));
        $waysGroup = new SubGroup();
        $waysGroup->setName('waysGroup');
        $waysGroup->setTitle(Yii::t('app', 'Маршруты:'));

        $userIcon = new Icon([
            'iconUrl' => '/images/position_worker_m.png',
            'iconSize' => new Point(['x' => 28, 'y' => 43]),
            'iconAnchor' => new Point (['x' => 14, 'y' => 43]),
            'popupAnchor' => new Point (['x' => -3, 'y' => -76])
        ]);

        $count = 0;
        foreach ($users as $current_user) {
            $userData[$count]['_id'] = $current_user['_id'];
            $userData[$count]['username'] = $current_user['username'];

            $gpsQuery = Routes::find()
                ->select('latitude, longitude, date')
                ->where(['userId' => $current_user['id']])
                ->limit(10000);
            $gps = $gpsQuery->orderBy('date DESC')->asArray()->all();
            /*            $deep = Settings::getSettings(Settings::SETTING_GPS_TRACK_DEEP);
                        if ($deep == "0") {
                            $gps = $gpsQuery->orderBy('date DESC')->asArray()->all();
                        } else {
                            $gps = $gpsQuery->andWhere('date >= date_sub(now(),INTERVAL ' . $deep . ' DAY)')
                                ->orderBy('date DESC')->asArray()->all();
                        }*/
            if ($gps) {
                $lats[$count] = $gps;
                $userData[$count]['latitude'] = $gps[0]['latitude'];
                $userData[$count]['longitude'] = $gps[0]['longitude'];
            } else {
                $lats[$count] = [];
                $userData[$count]['latitude'] = 0;
                $userData[$count]['longitude'] = 0;
            }
            $count++;
        }

        $wayGroup = [];
        $cnt = 0;
        foreach ($userData as $user) {
            $wayGroup[$cnt] = new SubGroup();
            $wayGroup[$cnt]->setTitle($user["name"]);
            if (count($lats[$cnt]) > 0) {
                $latLngs = [];
                foreach ($lats[$cnt] as $lat) {
                    $latLng = new LatLng(['lat' => $lat["latitude"], 'lng' => $lat["longitude"]]);
                    $latLngs[] = $latLng;
                }
                $wayUsers[$cnt] = new PolyLine();
                $wayUsers[$cnt]->setLatLngs($latLngs);
                $wayUsers[$cnt]->clientOptions = [
                    'color' => '#' . MainFunctions::random_color()
                ];
                $waysGroup->addLayer($wayUsers[$cnt]);
                $wayGroup[$cnt]->addLayer($wayUsers[$cnt]);
            }
            $wayGroup[$cnt]->setName('wayGroup' . $cnt);
            $cnt++;
        }

        $layer['usersGroup'] = $usersGroup;
        $layer['waysGroup'] = $waysGroup;
        $layer['wayGroup'] = $wayGroup;
        $layer['coordinates'] = $coordinates;
        $layer['userData'] = $userData;
        return $layer;
    }

    /**
     * Signs user up.
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Dashboard
     *
     * @return string
     * @throws Exception
     */
    public function actionDashboard()
    {
        return $this->render(
            'dashboard', []
        );
    }

    /**
     * Login action.
     *
     * @return string
     */
    public
    function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            /** @var User $user */
            $user = Yii::$app->user->identity;

            $log = new Register();
            $log->userId = $user->id;
            $userIP = empty(Yii::$app->request->userIP) ? 'unknown' : Yii::$app->request->userIP;
            $log->title = 'Пользователь зашел в Систему с ' . $userIP;
            if (!$log->save()) {
                $errors = $log->errors;
                foreach ($errors as $error) {
                    Yii::error($error, "frontend/controllers/SiteController.php");
                }
            }
            return $this->goHome();
        } else {
            return $this->render(
                'login',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Action error
     *
     * @return string
     */
    public
    function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render("../site/error", [
                'name' => 'Ошибка',
                'message' => $exception->getMessage(),
            ]);
        }
        return '';
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public
    function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     */
    public
    function actionConfig()
    {
        $this->enableCsrfValidation = false;
        return $this->redirect($_SERVER["HTTP_REFERER"]);
    }
}
