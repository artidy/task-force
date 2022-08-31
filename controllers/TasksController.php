<?php

namespace app\controllers;

use app\models\Tasks;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex(): string
    {
        $tasks = Tasks::findAll();

        return $this->render('index', ['tasks', $tasks]);
    }

}
