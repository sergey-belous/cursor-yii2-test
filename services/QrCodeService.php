<?php

namespace app\services;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    public function generateDataUri(string $data): string
    {
        $qrCode = new QrCode(
            data: $data,
            size: 280,
            margin: 12,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return sprintf(
            'data:%s;base64,%s',
            $result->getMimeType(),
            base64_encode($result->getString())
        );
    }
}
