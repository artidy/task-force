<?php

namespace app\models;

use DateTime;
use Exception;
use Yii;
use PDO;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property int $is_performer
 * @property string|null $description
 * @property int $city_id
 * @property string|null $avatar_path
 * @property string|null $birthday
 * @property string|null $phone_number
 * @property string|null $telegram
 * @property string|null $registered_at
 * @property boolean $hide_contacts
 *
 * @property CanceledTasks[] $canceledTasks
 * @property Reviews[] $reviews
 * @property Tasks[] $tasks
 * @property Tasks[] $assignedTasks
 * @property UserSpecializations[] $userSpecializations
 * @property Cities $city
 */
class User extends BasedUser
{
    public string $password_repeat = '';

    public string $old_password = '';
    public string $new_password = '';
    public string $new_password_repeat = '';

    /**
     * @var ?UploadedFile
     */
    public ?UploadedFile $avatarFile = null;

    /**
     * {}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * {}
     */
    public function rules(): array
    {
        return [
            [['city_id', 'password'], 'required', 'on' => 'insert'],
            [['new_password'], 'required', 'when' => function ($model) {
                return $model->old_password;
            }],
            [['password_repeat', 'categories', 'old_password', 'new_password', 'new_password_repeat'], 'safe'],
            [['avatarFile'], 'file', 'mimeTypes' => ['image/jpeg', 'image/png'], 'extensions' => ['png', 'jpg', 'jpeg']],
            [['password'], 'compare', 'on' => 'insert'],
            [['new_password'], 'compare', 'on' => 'update'],
            [['registered_at'], 'date', 'format' => 'php:Y-m-d',],
            [['is_performer', 'hide_contacts'], 'boolean'],
            [['phone_number'], 'match', 'pattern' => '/^[+-]?\d{11}$/', 'message' => 'Номер телефона должен быть строкой в 11 символов'],
            [['email', 'name'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['phone_number'], 'number'],
            [['password', 'telegram'], 'string', 'max' => 64],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Пароль',
            'name' => 'Имя',
            'is_performer' => 'Исполнитель',
            'avatar_path' => 'Аватар',
            'birthday' => 'Дата рождения',
            'phone_number' => 'Номер телефона',
            'telegram' => 'Телеграм',
            'description' => 'Дополнительная информация',
            'city_id' => 'Местоположение',
            'registered_at' => 'Дата регистрации',
            'hide_contacts' => 'Показывать контакты только заказчику',
            'old_password' => 'Старый пароль',
            'new_password' => 'Новый пароль',
            'password_repeat' => 'Повтор пароля',
            'new_password_repeat' => 'Повтор пароля',
        ];
    }

    /**
     * Gets query for [[CanceledTasks]].
     *
     * @return ActiveQuery
     */
    public function getCanceledTasks(): ActiveQuery
    {
        return $this->hasMany(CanceledTasks::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Reviews::class, ['reviewer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[AssignedTasks]].
     *
     * @return ActiveQuery
     */
    public function getAssignedTasks(): ActiveQuery
    {
        return $this->hasMany(Tasks::class, ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[UserSpecializations]].
     *
     * @return ActiveQuery
     */
    public function getUserSpecializations(): ActiveQuery
    {
        return $this->hasMany(UserSpecializations::class, ['user_id' => 'id']);
    }

    public function getRating(): ?float
    {
        $rating = null;

        $opinionsCount = $this->getReviews()->count();

        if ($opinionsCount) {
            $ratingSum = $this->getReviews()->sum('rating');
            $failCount = $this->getCanceledTasks()->count();
            $rating = round(intdiv($ratingSum, $opinionsCount + $failCount), 2);
        }

        return $rating;
    }

    /**
     * @throws Exception
     */
    public function getRatingPosition(): int|string|null
    {
        $result = null;

        $sql = "SELECT u.id, (SUM(o.rating) / (COUNT(o.id) + COUNT(ct.id))) as rate FROM users u
                INNER JOIN tasks t on u.id = t.performer_id
                LEFT JOIN reviews o on t.client_id = o.reviewer_id
                LEFT JOIN canceled_tasks ct on u.id = ct.user_id      
                WHERE t.status_id = 4
                GROUP BY u.id
                ORDER BY rate DESC";

        $records = Yii::$app->db->createCommand($sql)->queryAll(PDO::FETCH_ASSOC);
        $index = array_search($this->id, array_column($records, 'id'));

        if ($index !== false) {
            $result = $index + 1;
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function getAge(): ?int
    {
        $result = null;

        if ($this->birthday) {
            $bd = new DateTime($this->birthday);
            $now = new DateTime();
            $diff = $now->diff($bd);
            $result = $diff->y;
        }

        return $result;
    }

    public function isBusy(): bool
    {
        return $this->getAssignedTasks()->joinWith('status', true, 'INNER JOIN')->
            where(['statuses.id' => Statuses::STATUS_IN_PROGRESS])->exists();
    }

    public function isContactsAllowed(IdentityInterface $user)
    {
        $result = true;

        if ($this->hide_contacts) {
            $result = $this->getAssignedTasks($user)->exists();
        }

        return $result;
    }

    public function getTasksByStatus($status): ActiveQuery
    {
        $user_type = $this->is_performer ? 'performer_id' : 'client_id';
        $query = Tasks::find();
        $query->joinWith('status s');

        switch ($status) {
            case 'new':
                $query->where(['s.code' => Statuses::STATUS_NEW]);
                break;
            case 'close':
                $query->where(['s.code' => [Statuses::STATUS_COMPLETE, Statuses::STATUS_EXPIRED, Statuses::STATUS_CANCEL]]);
                break;
            case 'in_progress':
                $query->where(['s.code' => Statuses::STATUS_IN_PROGRESS]);
                break;
            case 'expired':
                $query->where(['s.code' => Statuses::STATUS_IN_PROGRESS])
                    ->andWhere(['<', 'deadline', date('Y-m-d')]);
                break;
        }

        $query->andWhere("$user_type = :user_id", [':user_id' => $this->id]);

        return $query;
    }

    public function addCanceledTask(int $task_id, string $description)
    {
        $canceledTask = new CanceledTasks();
        $canceledTask->task_id = $task_id;
        $canceledTask->description = $description;
        $canceledTask->user_id = $this->id;
    }
}
