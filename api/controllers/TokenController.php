<?php

namespace api\controllers;

use common\models\Token;
use common\models\User;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class TokenController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Возвращает токен по Id.
     *
     * @param $id
     * @return Token Объект токена.
     */
    public static function getTokenById($id)
    {
        $tokens = Token::find()->where(['id' => $id])->all();
        if (count($tokens) > 1) {
            return null;
        } else if (count($tokens) == 1) {
            return $tokens[0];
        } else {
            return null;
        }
    }

    /**
     * Проверяет действителен ли указанный токен.
     *
     * @param string $token Токен.
     *
     * @return boolean
     */
    public static function isTokenValid($token)
    {
        if ($token == null) {
            return false;
        }

        return Token::isTokenValid($token);
    }

    /**
     * Возвращает токен из переданного запроса.
     *
     * @param Request $request Запрос.
     *
     * @return string Токен.
     */
    public static function getTokenString($request)
    {
        if ($request == null) {
            return null;
        }

        $value = $request->getHeaders()->get('Authorization');
        if ($value == null) {
            return null;
        } else {
            $result = explode('bearer ', $value);
            if (count($result) == 2) {
                return $result[1];
            } else {
                return null;
            }
        }
    }

    /**
     * Получаем пользователя по токену.
     *
     * @param string $token Токен.
     *
     * @return User Пользователь.
     */
    public static function getUserByToken($token)
    {
        $tokens = Token::find()->where(['accessToken' => $token])->all();
        if (count($tokens) > 1) {
            return null;
        } else if (count($tokens) == 1) {
            $user = User::find()->where(['id' => $tokens[0]->id])->all();
            return $user[0];
        } else {
            return null;
        }
    }

    /**
     * Создаёт и возвращает новый токен.
     *
     * @return string Токен.
     * @throws NotAcceptableHttpException
     * @throws HttpException
     * @throws UnauthorizedHttpException
     * @throws \Exception
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new NotAcceptableHttpException();
        }

        $tokenType = $request->post('grant_type');
        // TODO: пароль и ид метки в открытом виде гонять не целесообразно,
        // нужно что-то придумать!
        $password = $request->post($tokenType);

        // находим пользователя с таким паролем
        $user = self::getUser($tokenType, $password);
        if ($user == null) {
            throw new UnauthorizedHttpException();
        }

        $token = Token::findOne(['id' => $user->_id]);
        if ($token != null) {
            $start = time();
            $end = $start + 86400;
            $token->accessToken = Token::initToken();
            $token->tokenType = $tokenType;
            $token->expiresIn = $end;
            $token->userName = $user->email;
            $token->issued = date('Y-m-d\TH:i:s', $start);
            $token->expires = date('Y-m-d\TH:i:s', $end);
            $token->save();
        } else {
            // создаём токен
            $token = self::createToken(
                $user->email, $user->id, $tokenType
            );

            if ($token == null) {
                throw new HttpException(500, Yii::t('app', 'Ошибка получения токена!'));
            }
        }

        return $token;
    }

    /**
     * Возвращает пользователя по паролю либо метке.
     *
     * @param string $tokenType Тип токена (label, password)
     * @param string $password Ид метки или пароль.
     *
     * @return User
     * @throws NotAcceptableHttpException
     * @throws HttpException
     */
    public static function getUser($tokenType, $password)
    {
        $condition = null;
        switch ($tokenType) {
            case 'label':
                $condition = ['id' => $password];
                break;
            case 'password':
                $condition = ['pass' => $password];
                break;
            default:
                throw new NotAcceptableHttpException();
        }

        // пользователь обязательно должен быть 'активным'
        $condition['active'] = 1;
        $users = User::find()->where($condition)->all();
        if (count($users) > 1) {
            return null;
        } else if (count($users) == 1) {
            return $users[0];
        } else {
            return null;
        }
    }

    /**
     * Создаёт новый токен.
     *
     * @param string $login Login пользователя.
     * @param string $id Id метки.
     * @param string $tokenType Тип токена (label | password)
     *
     * @return Token | null
     * @throws \Exception
     */
    public static function createToken($login, $id, $tokenType)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($login == null || $id == null || $tokenType == null) {
            return null;
        }

        $start = time();
        $end = $start + 86400;

        $token = new Token();
        $token->id = $id;
        $token->accessToken = Token::initToken();
        $token->tokenType = $tokenType;
        $token->expiresIn = $end;
        $token->userName = $login;
        $token->issued = date('Y-m-d\TH:i:s', $start);
        $token->expires = date('Y-m-d\TH:i:s', $end);

        if (!$token->save()) {
            return null;
        }

        return $token;
    }
}
