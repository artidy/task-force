<?php

namespace app\controllers;
use app\models\Cities;
use app\models\LoginForm;
use app\models\User;
use yii\web\Controller;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

class AuthController extends Controller
{
    public function actionSignup(): array|string
    {
        $user = new User(['scenario' => 'insert']);
        $cities = Cities::find()->orderBy('title')->all();

        if (Yii::$app->request->getIsPost()) {
            $user->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($user);
            }

            if ($user->validate()) {
                $user->password = Yii::$app->security->generatePasswordHash($user->password);

                $user->save(false);
                $this->goHome();
            }
        }

        return $this->render('signup', ['user' => $user, 'cities' => $cities]);
    }

    public function actionLogin()
    {
        $loginForm = new LoginForm();

        if (Yii::$app->request->getIsPost()) {
            $loginForm->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($loginForm);
            }

            if ($loginForm->validate()) {
                Yii::$app->user->login($loginForm->getUser());

                return $this->goHome();
            }
        }
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
