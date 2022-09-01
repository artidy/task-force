<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Reply;
use app\models\Reviews;
use app\models\Tasks;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class TasksController extends SecuredController
{
    public function actionIndex(): string
    {
        $task_instance = new Tasks();
        $task_instance->load(Yii::$app->request->post());

        $tasksQuery = $task_instance->getSearchQuery();
        $categories = Categories::find()->all();

        $countQuery = clone $tasksQuery;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 5]);
        $tasks = $tasksQuery->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            'tasks' => $tasks,
            'pages' => $pages,
            'task_instance' => $task_instance,
            'categories' => $categories
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $task = $this->findOrDie($id, Tasks::class);
        $reply = new Reply;
        $reviews = new Reviews;

        return $this->render('view', ['task' => $task, 'newReply' => $reply, 'reviews' => $reviews]);
    }
}
