<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $short_link_id
 * @property string $ip_address
 * @property int $created_at
 *
 * @property ShortLink $shortLink
 */
class ClickLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%click_log}}';
    }

    public function rules(): array
    {
        return [
            [['short_link_id', 'ip_address'], 'required'],
            [['short_link_id', 'created_at'], 'integer'],
            [['ip_address'], 'string', 'max' => 45],
            [
                ['short_link_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ShortLink::class,
                'targetAttribute' => ['short_link_id' => 'id'],
            ],
        ];
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function getShortLink(): ActiveQuery
    {
        return $this->hasOne(ShortLink::class, ['id' => 'short_link_id']);
    }
}
