<?php

namespace app\controllers;

use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\IdentityInterface;

abstract class SecuredController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $id
     * @param $modelClass
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findOrDie($id, $modelClass): ActiveRecord
    {
        $reply = $modelClass::findOne($id);

        if (!$reply) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $reply;
    }

    /**
     * @throws Throwable
     */
    public function getUser(): bool|IdentityInterface|null
    {
        return Yii::$app->user->getIdentity();
    }
}
