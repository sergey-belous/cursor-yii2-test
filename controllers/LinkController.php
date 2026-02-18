<?php

namespace app\controllers;

use app\models\forms\ShortenUrlForm;
use app\models\ShortLink;
use app\services\QrCodeService;
use app\services\UrlAvailabilityService;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class LinkController extends Controller
{
    private UrlAvailabilityService $urlAvailabilityService;
    private QrCodeService $qrCodeService;

    public function init(): void
    {
        parent::init();
        $this->urlAvailabilityService = new UrlAvailabilityService();
        $this->qrCodeService = new QrCodeService();
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ShortenUrlForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => current($model->getFirstErrors()) ?: 'Проверьте корректность URL.',
            ];
        }

        if (!$this->urlAvailabilityService->isAvailable($model->url)) {
            return [
                'success' => false,
                'message' => 'Данный URL не доступен',
            ];
        }

        try {
            $shortLink = new ShortLink([
                'original_url' => $model->url,
                'short_code' => ShortLink::generateUniqueCode(),
            ]);

            if (!$shortLink->save()) {
                Yii::error($shortLink->errors, __METHOD__);

                return [
                    'success' => false,
                    'message' => 'Не удалось сохранить ссылку.',
                ];
            }

            $shortUrl = Url::to(['/redirect/go', 'code' => $shortLink->short_code], true);

            return [
                'success' => true,
                'shortUrl' => $shortUrl,
                'qrCode' => $this->qrCodeService->generateDataUri($shortUrl),
            ];
        } catch (Throwable $exception) {
            Yii::error($exception->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;

            return [
                'success' => false,
                'message' => 'Внутренняя ошибка сервиса.',
            ];
        }
    }
}
