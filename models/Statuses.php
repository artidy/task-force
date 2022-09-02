<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "statuses".
 *
 * @property int $id
 * @property string $title
 * @property string $code
 *
 * @property Tasks[] $tasks
 */
class Statuses extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'proceed';
    const STATUS_CANCEL = 'cancel';
    const STATUS_COMPLETE = 'complete';
    const STATUS_EXPIRED = 'expired';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'code'], 'required'],
            [['title', 'code'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'code' => 'Code',
        ];
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['status_id' => 'id']);
    }

    /**
     * Finds status by code
     *
     * @param string $code
     * @return Statuses
     */
    public static function findByCode(string $code): static
    {
        return static::findOne(['code' => $code]);
    }
}
