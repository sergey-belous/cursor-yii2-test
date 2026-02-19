<?php

namespace app\controllers\api;

use app\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;

class AuthController extends ApiController
{
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'login' => ['post'],
                    'logout' => ['post'],
                    'me' => ['get'],
                ],
            ],
        ];
    }

    public function actionMe(): array
    {
        if (Yii::$app->user->isGuest) {
            return [
                'authenticated' => false,
                'user' => null,
            ];
        }

        return [
            'authenticated' => true,
            'user' => [
                'id' => Yii::$app->user->id,
                'username' => Yii::$app->user->identity->username,
            ],
        ];
    }

    public function actionLogin(): array
    {
        if (!Yii::$app->user->isGuest) {
            return [
                'success' => true,
                'message' => 'Вы уже авторизованы.',
                'user' => [
                    'id' => Yii::$app->user->id,
                    'username' => Yii::$app->user->identity->username,
                ],
            ];
        }

        $model = new LoginForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->login()) {
            Yii::$app->response->statusCode = 422;

            return [
                'success' => false,
                'message' => current($model->getFirstErrors()) ?: 'Неверные учетные данные.',
                'errors' => $model->getErrors(),
            ];
        }

        return [
            'success' => true,
            'message' => 'Вход выполнен.',
            'user' => [
                'id' => Yii::$app->user->id,
                'username' => Yii::$app->user->identity->username,
            ],
        ];
    }

    public function actionLogout(): array
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        return [
            'success' => true,
            'message' => 'Выход выполнен.',
        ];
    }
}
