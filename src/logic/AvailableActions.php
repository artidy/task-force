<?php

namespace AndreyPechennikov\TaskForce\logic;

use AndreyPechennikov\TaskForce\logic\actions\AbstractAction;
use AndreyPechennikov\TaskForce\logic\actions\CancelAction;
use AndreyPechennikov\TaskForce\logic\actions\CompleteAction;
use AndreyPechennikov\TaskForce\logic\actions\DenyAction;
use AndreyPechennikov\TaskForce\logic\actions\ResponseAction;
use DateTime;

class AvailableActions
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'proceed';
    const STATUS_CANCEL = 'cancel';
    const STATUS_COMPLETE = 'complete';
    const STATUS_EXPIRED = 'expired';

    const ROLE_PERFORMER = 'performer';
    const ROLE_CLIENT = 'customer';

    private ?int $performerId = null;
    private ?int $clientId = null;

    private ?string $status = null;
    private ?DateTime $deadline = null;

    /**
     * AvailableActionsStrategy constructor.
     * @param string $status
     * @param int|null $performerId
     * @param int $clientId
     */
    public function __construct(string $status, int $clientId, ?int $performerId)
    {
        $this->setStatus($status);

        $this->clientId = $clientId;
        $this->performerId = $performerId;
    }

    public function setDeadline(DateTime $deadline): void
    {
        $currentDate = new DateTime();

        if ($deadline > $currentDate) {
            $this->deadline = $deadline;
        }
    }

    public function getAvailableActions(string $role, int $id): array
    {
        $statusActions = $this->statusAllowedActions($this->status);
        $roleActions = $this->roleAllowedActions($role);

        $allowedActions = array_intersect($statusActions, $roleActions);

        $allowedActions = array_filter($allowedActions, function (AbstractAction $action) use ($id) {
            return $action->checkRights($id, $this->clientId, $this->performerId);
        });

        return array_values($allowedActions);
    }

    public function getNextStatus(string $action): string|null
    {
        $map = [
            CompleteAction::getIdentifier() => self::STATUS_COMPLETE,
            CancelAction::getIdentifier() => self::STATUS_CANCEL,
            DenyAction::getIdentifier() => self::STATUS_CANCEL
        ];

        return $map[$action] || null;
    }

    public function setStatus(string $status): void
    {
        $availableStatuses = [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_CANCEL,
            self::STATUS_COMPLETE,
            self::STATUS_EXPIRED
        ];

        if (in_array($status, $availableStatuses)) {
            $this->status = $status;
        }
    }

    /**
     * Возвращает действия, доступные для каждой роли
     * @param string $role
     * @return array
     */
    private function roleAllowedActions(string $role): array
    {
        $map = [
            self::ROLE_CLIENT => [CancelAction::class, CompleteAction::class],
            self::ROLE_PERFORMER => [ResponseAction::class, DenyAction::class],
        ];

        return $map[$role] ?? [];
    }

    /**
     * Возвращает действия, доступные для каждого статуса
     * @param string $status
     * @return array
     */
    private function statusAllowedActions(string $status): array
    {
        $map = [
            self::STATUS_IN_PROGRESS => [DenyAction::class, CompleteAction::class],
            self::STATUS_NEW => [CancelAction::class, ResponseAction::class],
        ];

        return $map[$status] ?? [];
    }

    private function getStatusMap(string $status): array
    {
        $map = [
            self::STATUS_NEW => [self::STATUS_EXPIRED, self::STATUS_CANCEL],
            self::STATUS_IN_PROGRESS => [self::STATUS_CANCEL, self::STATUS_COMPLETE],
            self::STATUS_EXPIRED => [self::STATUS_CANCEL]
        ];

        return $map[$status];
    }
}
