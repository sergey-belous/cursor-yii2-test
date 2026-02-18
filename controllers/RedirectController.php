<?php

namespace app\controllers;

use app\models\ClickLog;
use app\models\ShortLink;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RedirectController extends Controller
{
    public function actionGo(string $code): Response
    {
        $shortLink = ShortLink::findOne(['short_code' => $code]);
        if ($shortLink === null) {
            throw new NotFoundHttpException('Короткая ссылка не найдена.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$shortLink->updateCounters(['visits_count' => 1])) {
                throw new RuntimeException('Failed to increment visits_count.');
            }

            $clickLog = new ClickLog([
                'short_link_id' => $shortLink->id,
                'ip_address' => $this->resolveClientIp(),
            ]);
            if (!$clickLog->save(false)) {
                throw new RuntimeException('Failed to save click log.');
            }

            $transaction->commit();
        } catch (Throwable $exception) {
            $transaction->rollBack();
            Yii::warning($exception->getMessage(), __METHOD__);
        }

        return $this->redirect($shortLink->original_url);
    }

    private function resolveClientIp(): string
    {
        $forwardedFor = Yii::$app->request->headers->get('X-Forwarded-For', '');
        if ($forwardedFor !== '') {
            foreach (explode(',', $forwardedFor) as $rawIp) {
                $ip = trim($rawIp);
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }

        $realIp = trim((string) Yii::$app->request->headers->get('X-Real-IP', ''));
        if (filter_var($realIp, FILTER_VALIDATE_IP) !== false) {
            return $realIp;
        }

        $userIp = (string) Yii::$app->request->userIP;
        if (filter_var($userIp, FILTER_VALIDATE_IP) !== false) {
            return $userIp;
        }

        return '0.0.0.0';
    }
}
