<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Tasks;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class TasksController extends Controller
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

}
