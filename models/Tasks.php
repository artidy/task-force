<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $category_id
 * @property int $client_id
 * @property string $status
 * @property int|null $performer_id
 * @property int|null $location_id
 * @property int|null $budget
 * @property string|null $deadline
 *
 * @property CanceledTasks[] $canceledTasks
 * @property Categories $category
 * @property Users $client
 * @property Files[] $files
 * @property Cities $location
 * @property Users $performer
 * @property Reviews[] $reviews
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'category_id', 'client_id', 'status'], 'required'],
            [['category_id', 'client_id', 'performer_id', 'location_id', 'budget'], 'integer'],
            [['deadline'], 'safe'],
            [['title', 'status'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 320],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['performer_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['location_id' => 'id']],
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
            'category_id' => 'Category ID',
            'client_id' => 'Client ID',
            'status' => 'Status',
            'performer_id' => 'Performer ID',
            'location_id' => 'Location ID',
            'budget' => 'Budget',
            'deadline' => 'Deadline',
        ];
    }

    /**
     * Gets query for [[CanceledTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanceledTasks()
    {
        return $this->hasMany(CanceledTasks::className(), ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::className(), ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Users::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(Files::className(), ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Location]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Cities::className(), ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Performer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformer()
    {
        return $this->hasOne(Users::className(), ['id' => 'performer_id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['task_id' => 'id']);
    }
}
