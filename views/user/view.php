<?php

use app\helpers\UIHelper;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Профиль пользователя';
/**
 * @var User $user
 * @var View $this
 */
?>

<div class="left-column">
    <h3 class="head-main"><?= Html::encode($user->name); ?></h3>
    <div class="user-card">
        <div class="photo-rate">
            <img
                class="card-photo"
                src="<?= "assets/img/" . Html::encode($user->avatar_path); ?>"
                width="191"
                alt="Фото пользователя"
            >
            <div class="card-rate">
                <?= UIHelper::showStarRating($user->getRating(), 'big'); ?>
                <span class="current-rate"><?= $user->getRating(); ?></span>
            </div>
        </div>
        <p class="user-description">
            <?= Html::encode($user->description); ?>
        </p>
    </div>
    <div class="specialization-bio">
        <div class="specialization">
            <p class="head-info">Специализации</p>
            <ul class="special-list">
                <?php foreach ($user->userSpecializations as $user_specialization): ?>
                    <li class="special-item">
                        <a href="#" class="link link--regular">
                            <?= Html::encode($user_specialization->specialization->title); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bio">
            <p class="head-info">Био</p>
            <p class="bio-info"><span class="country-info">Россия</span>,
                <span class="town-info">Гамбург</span>
                <?php if ($user->birthday): ?>,
                    <span class="age-info"><?=$user->getAge(); ?></span> лет
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php if ($user->reviews): ?>
        <h4 class="head-regular">Отзывы заказчиков</h4>

        <?php foreach ($user->reviews as $review): ?>
            <div class="response-card">
                <img
                    class="customer-photo"
                    src="<?= "assets/img/" . Html::encode($review->reviewer->avatar_path); ?>"
                    width="120"
                    height="127"
                    alt="Аватар заказчика"
                >
                <div class="feedback-wrapper">
                    <p class="feedback">«<?= Html::encode($review->description); ?>»</p>
                    <p class="task">
                        Задание «<a href="<?=Url::to(['tasks/view', 'id' => $review->task_id]); ?>"
                        class="link link--small"><?=Html::encode($review->task->title); ?></a>» выполнено</p>
                </div>
                <div class="feedback-wrapper">
                    <?= UIHelper::showStarRating($review->rating); ?>
                    <p class="info-text">
                        <span class="current-time">
                            <?= Yii::$app->formatter->asRelativeTime($review->created_at); ?>
                        </span>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif ?>
</div>
<div class="right-column">
    <div class="right-card black">
        <h4 class="head-card">Статистика исполнителя</h4>
        <dl class="black-list">
            <dt>Всего заказов</dt>
            <dd><?= $user->getAssignedTasks()->count(); ?> выполнено,
                <?= $user->getCanceledTasks()->count(); ?> провалено</dd>
            <?php if ($position = $user->getRatingPosition()): ?>
                <dt>Место в рейтинге</dt>
                <dd><?= $position; ?> место</dd>
            <?php endif ?>
            <dt>Дата регистрации</dt>
            <dd><?= Yii::$app->formatter->asDate($user->registered_at); ?></dd>
            <dt>Статус</dt>
            <?php if (!$user->isBusy()): ?>
                <dd>Открыт для новых заказов</dd>
            <?php else: ?>
                <dd>Занят</dd>
            <?php endif ?>
        </dl>
    </div>
    <?php if ($user->isContactsAllowed()): ?>
        <div class="right-card white">
            <h4 class="head-card">Контакты</h4>
            <ul class="enumeration-list">
                <?php if ($user->phone_number): ?>
                    <li class="enumeration-item">
                        <a href="tel:<?= Html::encode($user->phone_number); ?>" class="link link--block link--phone">
                            <?= Html::encode($user->phone_number); ?>
                        </a>
                    </li>
                <?php endif ?>
                <li class="enumeration-item">
                    <a href="mailto:<?= Html::encode($user->email); ?>" class="link link--block link--email">
                        <?= Html::encode($user->email); ?>
                    </a>
                </li>
                <?php if ($user->telegram): ?>
                    <li class="enumeration-item">
                        <a href="https://t.me/<?= Html::encode($user->telegram); ?>"
                           class="link link--block link--tg">@<?= Html::encode($user->telegram); ?></a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    <?php endif ?>
</div>
