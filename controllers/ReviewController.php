<?php

namespace app\controllers;

use AndreyPechennikov\TaskForce\logic\actions\CompleteAction;
use app\models\Reviews;
use app\models\Tasks;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Yii;

class ReviewController extends SecuredController
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate($task): Response
    {
        /**
         * @var Tasks $task
         */
        $task = $this->findOrDie($task, Tasks::class);
        $review = new Reviews();

        if (Yii::$app->request->isPost) {
            $review->load(Yii::$app->request->post());

            if ($review->validate()) {
                $task->link('reviews', $review);
                $task->goToNextStatus(new CompleteAction);
            }
        }

        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }

    public function actionValidate()
    {
        $review = new Reviews();

        if (Yii::$app->request->isAjax && $review->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($review);
        }
    }

}
