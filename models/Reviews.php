<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reviews".
 *
 * @property int $id
 * @property int $task_id
 * @property string $description
 * @property int $rating
 * @property int $reviewer_id
 *
 * @property Users $reviewer
 * @property Tasks $task
 */
class Reviews extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'description', 'rating', 'reviewer_id'], 'required'],
            [['task_id', 'rating', 'reviewer_id'], 'integer'],
            [['description'], 'string', 'max' => 320],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['reviewer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['reviewer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'description' => 'Description',
            'rating' => 'Rating',
            'reviewer_id' => 'Reviewer ID',
        ];
    }

    /**
     * Gets query for [[Reviewer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviewer()
    {
        return $this->hasOne(Users::className(), ['id' => 'reviewer_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }
}
