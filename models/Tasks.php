<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $category_id
 * @property int $client_id
 * @property int|null $performer_id
 * @property int|null $location_id
 * @property int|null $budget
 * @property string|null $deadline
 * @property int $status_id
 * @property string|null $created_at
 *
 * @property CanceledTasks[] $canceledTasks
 * @property Categories $category
 * @property Users $client
 * @property Files[] $files
 * @property Cities $location
 * @property Users $performer
 * @property Reviews[] $reviews
 * @property Statuses $status
 */
class Tasks extends ActiveRecord
{
    public bool $noResponses = false;
    public bool $noLocation = false;
    public string $filterPeriod = "";

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'description', 'category_id', 'client_id', 'status_id'], 'required'],
            [['category_id', 'client_id', 'performer_id', 'location_id', 'budget', 'status_id'], 'integer'],
            [['deadline', 'created_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 320],
            [['noResponses', 'noLocation'], 'boolean'],
            [['filterPeriod'], 'number'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Statuses::class, 'targetAttribute' => ['status_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['client_id' => 'id']],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['performer_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['location_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'category_id' => 'Категория',
            'client_id' => 'Клиент',
            'performer_id' => 'Роль',
            'location_id' => 'Местоположение',
            'budget' => 'Бюджет',
            'deadline' => 'Дедлайн',
            'status_id' => 'Статус',
            'created_at' => 'Дата создания',
            'noLocation' => 'Без локации',
            'noResponses' => 'Без откликов',
        ];
    }

    public function getSearchQuery(): ActiveQuery
    {
        $query = self::find();
        $query->joinWith('status s')->andWhere('s.code = "new"');

        $query->andFilterWhere(['category_id' => $this->category_id]);

        if ($this->noLocation) {
            $query->andWhere('location_id IS NULL');
        }

        if ($this->noResponses) {
            $query->joinWith('reviews r')->andWhere('r.id is NULL');
        }

        if ($this->filterPeriod) {
            $query->andWhere(
                'UNIX_TIMESTAMP(tasks.created_at) > UNIX_TIMESTAMP() - :period',
                [':period' => $this->filterPeriod]
            );
        }

        return $query->orderBy('created_at DESC');
    }

    /**
     * Gets query for [[CanceledTasks]].
     *
     * @return ActiveQuery
     */
    public function getCanceledTasks(): ActiveQuery
    {
        return $this->hasMany(CanceledTasks::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(Files::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Location]].
     *
     * @return ActiveQuery
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Cities::class, ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Performer]].
     *
     * @return ActiveQuery
     */
    public function getPerformer(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'performer_id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Reviews::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return ActiveQuery
     */
    public function getStatus(): ActiveQuery
    {
        return $this->hasOne(Statuses::class, ['id' => 'status_id']);
    }
}
