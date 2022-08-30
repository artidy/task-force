<?php

namespace AndreyPechennikov\TaskForce\logic;

use DateTime;

class AvailableActions
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'proceed';
    const STATUS_CANCEL = 'cancel';
    const STATUS_COMPLETE = 'complete';
    const STATUS_EXPIRED = 'expired';

    const ACTION_RESPONSE = 'action_response';
    const ACTION_CANCEL = 'action_cancel';
    const ACTION_DENY = 'action_deny';
    const ACTION_COMPLETE = 'action_complete';

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
        $rightRestrictions = $this->getRightsPairs();

        $allowedActions = array_intersect($statusActions, $roleActions);

        $allowedActions = array_filter($allowedActions, function ($action) use ($rightRestrictions, $id) {
            return $rightRestrictions[$action]($id);
        });

        return array_values($allowedActions);
    }

    public function getNextStatus(string $action)
    {
        $map = [
            self::ACTION_COMPLETE => self::STATUS_COMPLETE,
            self::ACTION_CANCEL => self::STATUS_CANCEL,
            self::ACTION_DENY => self::STATUS_CANCEL,
            self::ACTION_RESPONSE => null
        ];

        return $map[$action];
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
            self::ROLE_CLIENT => [self::ACTION_CANCEL, self::ACTION_COMPLETE],
            self::ROLE_PERFORMER => [self::ACTION_RESPONSE, self::ACTION_DENY]
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
            self::STATUS_IN_PROGRESS => [self::ACTION_DENY, self::ACTION_COMPLETE],
            self::STATUS_NEW => [self::ACTION_CANCEL, self::ACTION_RESPONSE],
        ];

        return $map[$status] ?? [];
    }

    /**
     * Проверяет доступность каждого действия для пользователя
     * @return array
     */
    private function getRightsPairs(): array
    {
        return [
            self::ACTION_RESPONSE => function ($id) {
                return $id !== $this->performerId;
            },
            self::ACTION_DENY => function ($id) {
                return $id == $this->performerId;
            },
            self::ACTION_CANCEL => function ($id) {
                return $id == $this->clientId;
            },
            self::ACTION_COMPLETE => function($id) {
                return $id == $this->clientId;
            }
        ];
    }


    private function getStatusMap(): array
    {
        return [
            self::STATUS_NEW => [self::STATUS_EXPIRED, self::STATUS_CANCEL],
            self::STATUS_IN_PROGRESS => [self::STATUS_CANCEL, self::STATUS_COMPLETE],
            self::STATUS_CANCEL => [],
            self::STATUS_COMPLETE => [],
            self::STATUS_EXPIRED => [self::STATUS_CANCEL]
        ];
    }
}
