<?php

namespace app\controllers;

use app\models\User;
use Throwable;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

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

    /**
     * @throws Throwable
     */
    public function actionSettings(): Response|string
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $user->setScenario('update');

        if (Yii::$app->request->isPost) {
            $user->load(Yii::$app->request->post());
            $user->avatarFile = UploadedFile::getInstance($user, 'avatarFile');

            if ($user->save()) {
                return $this->redirect(['user/view', 'id' => $user->id]);
            }
        }

        return $this->render('settings', ['user' => $this->getUser()]);
    }
}
