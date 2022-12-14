<?php

namespace AndreyPechennikov\TaskForce\logic\actions;

class CancelAction extends AbstractAction
{
    public static function getLabel(): string
    {
        return 'Отменить';
    }

    public static function getIdentifier(): string
    {
        return 'cancel_action';
    }

    public static function checkRights(int $userId, ?int$clientId, ?int $performerId): bool
    {
        return $userId === $clientId;
    }
}
