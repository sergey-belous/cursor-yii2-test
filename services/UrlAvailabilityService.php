<?php

namespace app\services;

class UrlAvailabilityService
{
    private const CONNECT_TIMEOUT = 5;
    private const REQUEST_TIMEOUT = 8;
    private const USER_AGENT = 'Yii2ShortenerBot/1.0';

    public function isAvailable(string $url): bool
    {
        if ($this->checkUrl($url, true)) {
            return true;
        }

        return $this->checkUrl($url, false);
    }

    private function checkUrl(string $url, bool $headRequest): bool
    {
        $curl = curl_init($url);
        if ($curl === false) {
            return false;
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_NOBODY => $headRequest,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => self::REQUEST_TIMEOUT,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => self::USER_AGENT,
        ]);

        if (!$headRequest) {
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        }

        curl_exec($curl);

        $hasError = curl_errno($curl) !== 0;
        $httpCode = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        curl_close($curl);

        if ($hasError) {
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 400) {
            return true;
        }

        // Some resources block HEAD with 405/403 but still respond to GET.
        return $headRequest && in_array($httpCode, [403, 405], true);
    }
}
