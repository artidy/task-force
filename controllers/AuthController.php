<?php

namespace app\controllers;
use app\models\Auth;
use app\models\Cities;
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

        return $this->render('signup', ['model' => $user, 'cities' => $cities]);
    }
}
