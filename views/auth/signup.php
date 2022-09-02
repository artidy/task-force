<?php
/**
 * @var User $user
 * @var View $this
 * @var Cities $cities
 */

use app\models\Cities;
use app\models\User;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Регистрация пользователя';
?>

<div class="center-block">
    <div class="registration-form regular-form">
        <?php $form = ActiveForm::begin() ?>
            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <?= $form->field($user, 'name'); ?>
            <div class="half-wrapper">
                <?= $form->field($user, 'email'); ?>
                <?= $form->field($user, 'city_id')->dropDownList(array_column($cities, 'name', 'id')); ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($user, 'password')->passwordInput(); ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($user, 'password_repeat')->passwordInput(); ?>
            </div>
            <?= $form->field($user, 'is_performer')->
                checkbox(['labelOptions' => ['class' => 'control-label checkbox-label']]); ?>
            <input type="submit" class="button button--blue" value="Создать аккаунт">
        <?php ActiveForm::end(); ?>
    </div>
</div>
