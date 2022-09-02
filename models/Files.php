<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $name
 * @property string $path
 * @property int $user_id
 * @property string $created_at
 * @property string $task_uid
 * @property integer $size
 *
 * @property Tasks $task
 * @property User $user
 */
class Files extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public UploadedFile $file;

    /**
     * {}
     */
    public static function tableName(): string
    {
        return 'files';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
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
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg'],
            [['name', 'path', 'task_uid'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['path'], 'string', 'max' => 320],
            [['path'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'path' => 'Путь',
            'user_id' => 'Идентификатор пользователя',
            'created_at' => 'Дата создания',
        ];
    }

    public function upload(): bool
    {
        $this->name = $this->file->name;
        $newname = uniqid() . '.' . $this->file->getExtension();
        $this->path = '/uploads/' . $newname;
        $this->size = $this->file->size;

        if ($this->save()) {
            return $this->file->saveAs('@webroot/uploads/' . $newname);
        }

        return false;
    }

    /**
     * Gets query for [[Task]].
     *
     * @return ActiveQuery
     */
    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Tasks::class, ['uid' => 'task_uid']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
