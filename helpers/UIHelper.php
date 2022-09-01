<?php
namespace app\helpers;

use AndreyPechennikov\TaskForce\exception\StatusActionException;
use AndreyPechennikov\TaskForce\logic\actions\CancelAction;
use AndreyPechennikov\TaskForce\logic\actions\CompleteAction;
use AndreyPechennikov\TaskForce\logic\actions\DenyAction;
use AndreyPechennikov\TaskForce\logic\actions\ResponseAction;
use AndreyPechennikov\TaskForce\logic\AvailableActions;
use app\models\Tasks;
use app\models\Users;
use yii\helpers\Html;
use yii\helpers\Url;

class UIHelper
{
    public static function showStarRating($value, $size = 'small', $starsCount = 5, $active = false): string
    {
        $stars = '';

        for ($i = 1; $i <= $starsCount; $i++) {
            $className = $i <= $value ? 'fill-star' : '';
            $stars .= Html::tag('span', '&nbsp;', ['class' => $className]);
        }

        $className = 'stars-rating ' . $size;

        if ($active) {
            $className .= ' active-stars';
        }

        return Html::tag('div', $stars, ['class' => $className]);
    }

    public static function getActionButtons(Tasks $task, Users $user): array
    {
        $buttons = [];

        $colorsMap = [
            CancelAction::getIdentifier()   => 'orange',
            ResponseAction::getIdentifier() => 'blue',
            DenyAction::getIdentifier()     => 'pink',
            CompleteAction::getIdentifier() => 'yellow'
        ];

        $roleName = $user->is_performer ? AvailableActions::ROLE_PERFORMER : AvailableActions::ROLE_CLIENT;

        try {
            $availableActionsManger = new AvailableActions($task->status->code, $task->performer_id, $task->client_id);
            $actions = $availableActionsManger->getAvailableActions($roleName, $user->id);

            foreach ($actions as $action) {
                $color = $colorsMap[$action::getInternalName()];
                $label = $action::getLabel();

                $options = [
                    'data-action' => $action::getInternalName(),
                    'class' => 'button action-btn button--' . $color
                ];

                if ($action::getInternalName() === 'act_cancel') {
                    $options['href'] = Url::to(['tasks/cancel', 'id' => $task->id]);
                }

                $btn = Html::tag('a', $label, $options);

                $buttons[] = $btn;
            }
        } catch (StatusActionException $e) {
            error_log($e->getMessage());
        }

        return $buttons;
    }

    public static function getMyTasksMenu($isContractor): array
    {
        $items = [];

        if ($isContractor) {
            $items[] = ['label' => 'В процессе', 'url' => ['tasks/my', 'status' => 'in_progress']];
            $items[] = ['label' => 'Просрочено', 'url' => ['tasks/my', 'status' => 'expired']];
        }
        else {
            $items[] = ['label' => 'Новые', 'url' => ['tasks/my', 'status' => 'new']];
            $items[] = ['label' => 'В процессе', 'url' => ['tasks/my', 'status' => 'in_progress']];
        }
        $items[] = ['label' => 'Закрытые', 'url' => ['tasks/my', 'status' => 'close']];

        return $items;
    }

}
