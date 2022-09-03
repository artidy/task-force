<?php

namespace app\controllers;

use AndreyPechennikov\TaskForce\logic\actions\CancelAction;
use AndreyPechennikov\TaskForce\logic\actions\DenyAction;
use app\helpers\UIHelper;
use app\models\Categories;
use app\models\Cities;
use app\models\Files;
use app\models\Reply;
use app\models\Reviews;
use app\models\Tasks;
use app\models\User;
use Throwable;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

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

        return $this->render('view', ['task' => $task, 'newReply' => $reply, 'review' => $reviews]);
    }

    public function actionCreate()
    {
        $task = new Tasks();
        $categories = Categories::find()->all();
        $cities = Cities::find()->all();

        if (!Yii::$app->session->has('task_uid')) {
            Yii::$app->session->set('task_uid', uniqid('upload'));
        }

        if (Yii::$app->request->isPost) {
            $task->load(Yii::$app->request->post());
            $task->uid = Yii::$app->session->get('task_uid');
            $task->save();

            if ($task->id) {
                Yii::$app->session->remove('task_uid');
                return $this->redirect(['tasks/view', 'id' => $task->id]);
            }
        }

        return $this->render('create', ['task' => $task, 'categories' => $categories, 'cities' => $cities]);
    }

    /**
     * @throws Throwable
     */
    public function actionMy($status = null): string
    {
        $menuItems = UIHelper::getMyTasksMenu($this->getUser()->is_performer);

        if (!$status) {
            $this->redirect($menuItems[0]['url']);
        }

        $tasks = $this->getUser()->getTasksByStatus($status)->all();

        return $this->render('my', ['menuItems' => $menuItems, 'tasks' => $tasks]);
    }

    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {
            $model = new Files();
            $model->task_uid = Yii::$app->session->get('task_uid');
            $model->file = UploadedFile::getInstanceByName('file');

            $model->upload();

            return $this->asJson($model->getAttributes());
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCancel($id): Response
    {
        /**
         * @var Tasks $task
         */
        $task = $this->findOrDie($id, Tasks::class);
        $task->goToNextStatus(new CancelAction);

        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDeny($id): Response
    {
        /**
         * @var Tasks $task
         */
        $task = $this->findOrDie($id, Tasks::class);
        $task->goToNextStatus(new DenyAction());

        $performer = $task->performer;

        $performer->addCanceledTask($task->id, 'Отменил по собственному желанию');

        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }
}
