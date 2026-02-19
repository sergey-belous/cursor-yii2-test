<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * SPA entrypoint for root page.
     */
    public function actionIndex(): string
    {
        return $this->renderSpaShell();
    }

    /**
     * Explicit SPA entrypoint used by url rules.
     */
    public function actionApp(): string
    {
        return $this->renderSpaShell();
    }

    /**
     * Legacy route compatibility for /site/login.
     */
    public function actionLogin(): string
    {
        return $this->renderSpaShell();
    }

    /**
     * Legacy route compatibility for /site/contact.
     */
    public function actionContact(): string
    {
        return $this->renderSpaShell();
    }

    /**
     * Legacy route compatibility for /site/about.
     */
    public function actionAbout(): string
    {
        return $this->renderSpaShell();
    }

    /**
     * Legacy route compatibility for /site/logout.
     */
    public function actionLogout(): Response
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        return $this->redirect(['/']);
    }

    private function renderSpaShell(): string
    {
        $this->layout = 'spa';

        return $this->render('app');
    }
}
