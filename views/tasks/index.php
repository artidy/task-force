<?php
/* @var $this yii\web\View
 * @var $tasks Tasks[]
 * @var $task_instance Tasks
 * @var $pages Pagination
 * @var $categories Categories[]
 */

use app\models\Categories;
use app\models\Tasks;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Просмотр новых заданий';
?>
<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>
    <?php foreach ($tasks as $task): ?>
        <?=$this->render('//partials/_task', ['task' => $task]); ?>
    <?php endforeach; ?>
    <div class="pagination-wrapper">
        <?= LinkPager::widget([
            'pagination' => $pages,
            'options' => ['class' => 'pagination-list'],
            'prevPageCssClass' => 'pagination-item mark',
            'nextPageCssClass' => 'pagination-item mark',
            'pageCssClass' => 'pagination-item',
            'activePageCssClass' => 'pagination-item--active',
            'linkOptions' => ['class' => 'link link--page'],
            'nextPageLabel' => '',
            'prevPageLabel' => '',
            'maxButtonCount' => 5
        ]); ?>
    </div>
</div>
<div class="right-column">
    <div class="right-card black">
        <div class="search-form">
            <?php $form = ActiveForm::begin(); ?>
                <h4 class="head-card">Категории</h4>
                <div class="checkbox-wrapper">
                    <?= Html::activeCheckboxList(
                        $task_instance,
                        'category_id',
                        array_column($categories, 'title', 'id'),
                        ['tag' => null, 'itemOptions' => ['labelOptions' => ['class' => 'control-label']]]);
                    ?>
                </div>
                <h4 class="head-card">Дополнительно</h4>
                <div class="checkbox-wrapper">
                    <?= $form->field($task_instance, 'noResponses')->
                        checkbox(['labelOptions' => ['class' => 'control-label']]); ?>
                </div>
                <div class="checkbox-wrapper">
                    <?= $form->field($task_instance, 'noLocation')->
                        checkbox(['labelOptions' => ['class' => 'control-label']]); ?>
                </div>
                <h4 class="head-card">Период</h4>
                <div class="checkbox-wrapper">
                    <?= $form->field($task_instance, 'filterPeriod', ['template' => '{input}'])->
                        dropDownList([
                            '3600' => 'За последний час',
                            '86400' => 'За сутки',
                            '604800' => 'За неделю'
                        ], ['prompt' => 'Выбрать']);
                    ?>
                </div>
                <input type="submit" class="button button--blue" value="Искать">
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
