<?php

namespace AndreyPechennikov\TaskForce\logic\actions;

class CompleteAction extends AbstractAction
{
    public static function getLabel(): string
    {
        return 'Завершить';
    }

    public static function getIdentifier(): string
    {
        return 'complete_action';
    }

    public static function checkRights(int $userId, ?int$clientId, ?int $performerId): bool
    {
        return $userId === $clientId;
    }
}
