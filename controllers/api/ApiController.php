<?php

namespace app\controllers\api;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public $layout = false;

    public function beforeAction($action): bool
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if (!is_array($result)) {
            return $result;
        }

        $request = Yii::$app->request;
        $result['_csrf'] = [
            'param' => $request->csrfParam,
            'token' => $request->getCsrfToken(),
        ];

        return $result;
    }
}
