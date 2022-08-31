<?php

namespace app\controllers;

use AndreyPechennikov\TaskForce\converter\CsvSqlConverter;
use app\models\Tasks;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex(): string
    {
        $tasks = Tasks::findAll(['status_id' => 1]);

        $converter = new CsvSqlConverter('../data/csv');
        $converter->convertFiles('../data/sql', 'task_force');

        return $this->render('index', ['tasks' => $tasks]);
    }

}
