<?php

namespace app\models\forms;

use yii\base\Model;

class ShortenUrlForm extends Model
{
    public string $url = '';

    public function formName(): string
    {
        return '';
    }

    public function rules(): array
    {
        return [
            [['url'], 'required', 'message' => 'Введите URL.'],
            [['url'], 'trim'],
            [
                ['url'],
                'url',
                'validSchemes' => ['http', 'https'],
                'message' => 'Введите корректный URL (http/https).',
            ],
        ];
    }
}
