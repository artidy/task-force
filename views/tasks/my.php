<?php
/* @var $this View
* @var $menuItems array
* @var $tasks Tasks[]
*/

use app\models\Tasks;
use yii\web\View;
use yii\widgets\Menu;

$this->title = 'Мои задания';
?>

<div class="left-menu">
    <h3 class="head-main head-task">Мои задания</h3>
    <?= Menu::widget([
        'options' => ['class' => 'side-menu-list'], 'activeCssClass' => 'side-menu-item--active',
        'itemOptions' => ['class' => 'side-menu-item'],
        'linkTemplate' => '<a href="{url}" class="link link--nav">{label}</a>',
        'items' => $menuItems
    ]); ?>
</div>
<div class="left-column left-column--task">
    <h3 class="head-main head-regular">Новые задания</h3>
    <?php foreach ($tasks as $task): ?>
        <?=$this->render('//partials/_task', ['task' => $task]); ?>
    <?php endforeach; ?>
</div>
