<?php

namespace app\controllers\api;

use app\models\ContactForm;
use Yii;
use yii\filters\VerbFilter;

class ContactController extends ApiController
{
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'submit' => ['post'],
                ],
            ],
        ];
    }

    public function actionSubmit(): array
    {
        $model = new ContactForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->response->statusCode = 422;

            return [
                'success' => false,
                'message' => current($model->getFirstErrors()) ?: 'Не удалось отправить сообщение.',
                'errors' => $model->getErrors(),
            ];
        }

        return [
            'success' => true,
            'message' => 'Спасибо за сообщение. Мы ответим вам как можно скорее.',
        ];
    }
}
