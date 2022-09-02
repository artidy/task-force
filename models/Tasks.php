<?php

namespace app\models;

use AndreyPechennikov\TaskForce\logic\actions\AbstractAction;
use AndreyPechennikov\TaskForce\logic\AvailableActions;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

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
 * @property string|null $uid
 *
 * @property CanceledTasks[] $canceledTasks
 * @property Categories $category
 * @property User $client
 * @property Files[] $files
 * @property Cities $location
 * @property User $performer
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

    public function behaviors(): array
    {
        return [
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'client_id',
                'updatedByAttribute' => null
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['status_id'], 'default', 'value' => function($model, $attr) {
                return Statuses::find()->select('id')->where('id=1')->scalar();
            }],
            [['title', 'description', 'category_id', 'status_id'], 'required'],
            [['category_id', 'performer_id', 'location_id', 'budget', 'status_id'], 'integer'],
            [['deadline', 'created_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['budget'], 'integer', 'min' => 1],
            [['description'], 'string', 'max' => 320],
            [['uid'], 'string', 'max' => 64],
            [['noResponses', 'noLocation'], 'boolean'],
            [['filterPeriod'], 'number'],
            [['deadline'], 'date', 'format' => 'php:Y-m-d', 'min' => date('Y-m-d'), 'minString' => 'чем текущий день'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Statuses::class, 'targetAttribute' => ['status_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['performer_id' => 'id']],
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
        return $this->hasOne(User::class, ['id' => 'client_id']);
    }

    /**
     * Gets query for [[File]].
     *
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(Files::class, ['task_uid' => 'uid']);
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
        return $this->hasOne(User::class, ['id' => 'performer_id']);
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
     * Gets query for [[Replies]].
     *
     * @param IdentityInterface|null $user
     * @return ActiveQuery
     */
    public function getReplies(IdentityInterface $user = null): ActiveQuery
    {
        $allRepliesQuery = $this->hasMany(Reply::class, ['task_id' => 'id']);

        if ($user && $user->getId() !== $this->client_id) {
            $allRepliesQuery->where(['replies.user_id' => $user->getId()]);
        }

        return $allRepliesQuery;
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

    public function goToNextStatus(AbstractAction $action)
    {
        $actionManager = new AvailableActions($this->status->code, $this->client_id, $this->performer_id);
        $nextStatusName = $actionManager->getNextStatus($action::class);

        $status = Statuses::findOne(['code' => $nextStatusName]);
        $this->link('status', $status);
        $this->save();
    }
}
