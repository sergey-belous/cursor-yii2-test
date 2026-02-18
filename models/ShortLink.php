<?php

namespace app\models;

use RuntimeException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property int $visits_count
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ClickLog[] $clickLogs
 */
class ShortLink extends ActiveRecord
{
    private const CODE_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function tableName(): string
    {
        return '{{%short_link}}';
    }

    public function rules(): array
    {
        return [
            [['original_url', 'short_code'], 'required'],
            [['original_url'], 'string', 'max' => 2048],
            [['short_code'], 'string', 'max' => 16],
            [['visits_count', 'created_at', 'updated_at'], 'integer'],
            [['short_code'], 'unique'],
        ];
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    public function getClickLogs(): ActiveQuery
    {
        return $this->hasMany(ClickLog::class, ['short_link_id' => 'id']);
    }

    public static function generateUniqueCode(int $length = 8, int $maxAttempts = 20): string
    {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = self::generateRandomCode($length);
            if (!self::find()->where(['short_code' => $code])->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Unable to generate unique short code.');
    }

    private static function generateRandomCode(int $length): string
    {
        $characters = self::CODE_ALPHABET;
        $charactersLength = strlen($characters);
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, $charactersLength - 1);
            $code .= $characters[$index];
        }

        return $code;
    }
}
