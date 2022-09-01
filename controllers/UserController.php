<?php

namespace app\controllers;

use app\models\User;
use yii\web\NotFoundHttpException;

class UserController extends SecuredController
{

    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $user = $this->findOrDie($id, User::class);

        if (!$user->is_performer) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return $this->render('view', ['user' => $user]);
    }
}
