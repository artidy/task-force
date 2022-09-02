<?php

namespace app\models;

use DateTime;
use Exception;
use Yii;
use PDO;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property int $is_performer
 * @property string $description
 * @property int $city_id
 * @property string|null $avatar_path
 * @property string|null $birthday
 * @property string|null $phone_number
 * @property string|null $telegram
 * @property string|null $registered_at
 *
 * @property CanceledTasks[] $canceledTasks
 * @property Reviews[] $reviews
 * @property Tasks[] $tasks
 * @property Tasks[] $assignedTasks
 * @property UserSpecializations[] $userSpecializations
 */
class User extends BasedUser
{
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
            [['email', 'password', 'name', 'is_performer, description, city_id'], 'required'],
            [['is_performer, city_id'], 'integer'],
            [['birthday', 'registered_at'], 'safe'],
            [['email', 'avatar_path, description'], 'string', 'max' => 320],
            [['password'], 'string', 'max' => 256],
            [['name'], 'string', 'max' => 128],
            [['phone_number'], 'string', 'max' => 11],
            [['telegram'], 'string', 'max' => 64],
            [['email'], 'unique'],
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
     * @return \yii\db\ActiveQuery
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
        return $this->hasOne(Cities::class, ['city_id' => 'id']);
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
     * @throws \yii\db\Exception
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

    public function isContactsAllowed(): bool
    {
        return $this->getAssignedTasks()->exists();
    }
}
