<?php
/* @var $this yii\web\View
 * @var $task Tasks
 * @var $user Users
 * @var $newReply Reply
 */

use app\helpers\UIHelper;
use app\models\Reply;
use app\models\Tasks;
use app\models\Users;
use yii\helpers\Html;
use yii\helpers\Url;
use function morphos\Russian\pluralize;

$user = Yii::$app->user->getIdentity();
?>

<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?= Html::encode($task->title); ?></h3>
        <p class="price price--big"><?= $task->budget ? Html::encode($task->budget) . "₽" : ""; ?></p>
    </div>
    <p class="task-description"><?= Html::encode($task->description); ?></p>
    <a href="#" class="button button--blue action-btn" data-action="act_response">Откликнуться на задание</a>
    <a href="#" class="button button--orange action-btn" data-action="refusal">Отказаться от задания</a>
    <a href="#" class="button button--pink action-btn" data-action="completion">Завершить задание</a>
    <h4 class="head-regular">Отклики на задание</h4>
    <?php $replies = $task->getReplies($user)->all();
    foreach ($replies as $reply): ?>
        <div class="response-card">
            <img
                class="customer-photo"
                src="<?= "assets/img/" . Html::encode($reply->user->avatar_path); ?>"
                width="146"
                height="156"
                alt="Фото заказчиков"
            >
            <div class="feedback-wrapper">
                <a href="<?= Url::to(['user/view', 'id' => $reply->user_id]); ?>" class="link link--block link--big">
                    <?= Html::encode($reply->user->name); ?>
                </a>
                <div class="response-wrapper">
                    <?= UIHelper::showStarRating($reply->user->rating); ?>
                    <?php $reviewsCount = $reply->user->getReviews()->count(); ?>
                    <p class="reviews"><?= pluralize($reviewsCount, 'отзыв'); ?></p>
                </div>
                <p class="response-message">
                    <?= Html::encode($reply->message); ?>
                </p>
            </div>
            <div class="feedback-wrapper">
                <p class="info-text"><?= Yii::$app->formatter->asRelativeTime($reply->created_at); ?></p>
                <p class="price price--small"><?= Html::encode($reply->price); ?> ₽</p>
            </div>
            <div class="button-popup">
                <a href="#" class="button button--blue button--small">Принять</a>
                <a href="#" class="button button--orange button--small">Отказать</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="right-column">
    <div class="right-card black info-card">
        <h4 class="head-card">Информация о задании</h4>
        <dl class="black-list">
            <dt>Категория</dt>
            <dd><?= Html::encode($task->category->title) ?></dd>
            <dt>Дата публикации</dt>
            <dd><?= Yii::$app->formatter->asRelativeTime($task->created_at); ?></dd>
            <dt>Срок выполнения</dt>
            <dd><?= Yii::$app->formatter->asDatetime($task->deadline); ?></dd>
            <dt>Статус</dt>
            <dd><?= Html::encode($task->status->title); ?></dd>
        </dl>
    </div>
</div>
