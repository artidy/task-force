<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property int $is_performer
 * @property string|null $avatar_path
 * @property string|null $birthday
 * @property string|null $phone_number
 * @property string|null $telegram
 * @property string|null $registered_at
 *
 * @property CanceledTasks[] $canceledTasks
 * @property Reviews[] $reviews
 * @property Tasks[] $tasks
 * @property Tasks[] $tasks0
 * @property UserSpecializations[] $userSpecializations
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password', 'name', 'is_performer'], 'required'],
            [['is_performer'], 'integer'],
            [['birthday', 'registered_at'], 'safe'],
            [['email', 'avatar_path'], 'string', 'max' => 320],
            [['password'], 'string', 'max' => 256],
            [['name'], 'string', 'max' => 128],
            [['phone_number'], 'string', 'max' => 11],
            [['telegram'], 'string', 'max' => 64],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'name' => 'Name',
            'is_performer' => 'Is Performer',
            'avatar_path' => 'Avatar Path',
            'birthday' => 'Birthday',
            'phone_number' => 'Phone Number',
            'telegram' => 'Telegram',
            'registered_at' => 'Registered At',
        ];
    }

    /**
     * Gets query for [[CanceledTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanceledTasks()
    {
        return $this->hasMany(CanceledTasks::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['reviewer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks0()
    {
        return $this->hasMany(Tasks::className(), ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[UserSpecializations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSpecializations()
    {
        return $this->hasMany(UserSpecializations::className(), ['user_id' => 'id']);
    }
}
