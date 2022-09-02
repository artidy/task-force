<?php
/* @var $this View
 * @var $task Tasks
 * @var $user User
 * @var $newReply Reply
 * @var $review Reviews
 */

use app\helpers\UIHelper;
use app\models\Reply;
use app\models\Reviews;
use app\models\Tasks;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

use yii\widgets\ActiveForm;

use function morphos\Russian\pluralize;

$user = Yii::$app->user->getIdentity();
?>

<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?= Html::encode($task->title); ?></h3>
        <p class="price price--big"><?= $task->budget ? Html::encode($task->budget) . "₽" : ""; ?></p>
    </div>
    <p class="task-description"><?= Html::encode($task->description); ?></p>
    <?php foreach (UIHelper::getActionButtons($task, $user) as $button) {
            echo $button;
        }
    ?>
    <h4 class="head-regular">Отклики на задание</h4>
    <?php $replies = $task->getReplies($user)->all();
    foreach ($replies as $reply): ?>
        <div class="response-card">
            <img
                class="customer-photo"
                src="<?= "/assets/img/" . Html::encode($reply->user->avatar_path); ?>"
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
            <?php if ($user->id === $task->client_id && !$reply->is_accept && !$reply->is_denied): ?>
                <div class="button-popup">
                    <a
                        href="<?= Url::to(['reply/approve', 'id' => $reply->id]); ?>"
                        class="button button--blue button--small">Принять</a>
                    <a
                        href="<?= Url::to(['reply/deny', 'id' => $reply->id]); ?>"
                        class="button button--orange button--small">Отказать</a>
                </div>
            <?php endif; ?>
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
<section class="pop-up pop-up--deny_action pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Отказ от задания</h4>
        <p class="pop-up-text">
            <b>Внимание!</b><br>
            Вы собираетесь отказаться от выполнения этого задания.<br>
            Это действие плохо скажется на вашем рейтинге и увеличит счетчик проваленных заданий.
        </p>
        <a class="button button--pop-up button--orange" href="<?=Url::to(['tasks/deny', 'id' => $task->id]); ?>">Отказаться</a>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--complete_action pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Завершение задания</h4>
        <p class="pop-up-text">
            Вы собираетесь отметить это задание как выполненное.
            Пожалуйста, оставьте отзыв об исполнителе и отметьте отдельно, если возникли проблемы.
        </p>
        <div class="completion-form pop-up--form regular-form">
            <?php $form = ActiveForm::begin([
                'action' => Url::to(['review/create', 'task' => $task->id]),
                'enableAjaxValidation' => true,
                'validationUrl' => ['review/validate'],
            ]); ?>
                <?= $form->field($review, 'description')->textarea(); ?>
                <?= $form->field($review, 'rating', ['template' => '{label}{input}' .
                    UIHelper::showStarRating(0, 'big', 5, true) . '{error}'])
                    ->hiddenInput(); ?>
                <input type="submit" class="button button--pop-up button--blue" value="Завершить">
            <?php ActiveForm::end(); ?>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--response_action pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Добавление отклика к заданию</h4>
        <p class="pop-up-text">
            Вы собираетесь оставить свой отклик к этому заданию.
            Пожалуйста, укажите стоимость работы и добавьте комментарий, если необходимо.
        </p>
        <div class="addition-form pop-up--form regular-form">
            <?php $form = ActiveForm::begin(['enableAjaxValidation' => true,
                    'validationUrl' => ['reply/validate', 'task' => $task->id],
                    'action' => Url::to(['reply/create', 'task' => $task->id])]
                );
            ?>
                <?= $form->field($newReply, 'message')->textarea(); ?>
                <?= $form->field($newReply, 'price'); ?>
                <input type="submit" class="button button--pop-up button--blue" value="Отправить">
            <?php ActiveForm::end(); ?>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
