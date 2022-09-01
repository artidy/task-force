<?php
/**
 * @var $task Tasks
 */

use app\models\Tasks;
use yii\helpers\BaseStringHelper;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="task-card">
    <div class="header-task">
        <a href="<?= Url::toRoute(['tasks/view', 'id' => $task->id]); ?>" class="link link--block link--big">
            <?= Html::encode($task->title); ?>
        </a>
        <p class="price price--task"><?= Html::encode($task->budget); ?> ₽</p>
    </div>
    <p class="info-text"><?= Yii::$app->formatter->asRelativeTime($task->created_at); ?></p>
    <p class="task-text"><?= Html::encode(BaseStringHelper::truncate($task->description, 200)); ?>
    </p>
    <div class="footer-task">
        <?php if ($task->location): ?>
            <p class="info-text town-text"><?= Html::encode($task->location->title); ?></p>
        <?php endif ?>
        <p class="info-text category-text"><?= Html::encode($task->category->title); ?></p>
        <a href="<?=Url::toRoute(['tasks/view', 'id' => $task->id]); ?>" class="button button--black">Смотреть Задание</a>
    </div>
</div>
