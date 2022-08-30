<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "specializations".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 *
 * @property UserSpecializations[] $userSpecializations
 */
class Specializations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'specializations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 320],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[UserSpecializations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSpecializations()
    {
        return $this->hasMany(UserSpecializations::className(), ['specialization_id' => 'id']);
    }
}
