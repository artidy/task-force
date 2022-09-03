<?php

/**
 * @var View $this
 * @var LoginForm $user
 */

use app\models\LoginForm;
use yii\authclient\widgets\AuthChoice;use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Task Force';
?>
<section class="modal enter-form form-modal" id="enter-form">
    <h2>Вход на сайт</h2>
    <?php $form = ActiveForm::begin(['enableAjaxValidation' => true, 'action' => ['auth/login']]); ?>
    <?= $form->field($user, 'email', ['labelOptions' => ['class' => 'form-modal-description'],
        'inputOptions' => ['class' => 'enter-form-email input input-middle']]); ?>
    <?= $form->field($user, 'password', [ 'labelOptions' => ['class' => 'form-modal-description'],
        'inputOptions' => ['class' => 'enter-form-email input input-middle']])->passwordInput(); ?>
    <button class="button" type="submit">Войти</button>
    <?php ActiveForm::end(); ?>
    <?= AuthChoice::widget([
        'baseAuthUrl' => ['auth/vk'],
        'popupMode' => false,
    ]); ?>
    <button class="form-modal-close" type="button">Закрыть</button>
</section>
