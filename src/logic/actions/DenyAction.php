<?php

namespace AndreyPechennikov\TaskForce\logic\actions;

class DenyAction extends AbstractAction
{
    public static function getLabel(): string
    {
        return 'Отказаться';
    }

    public static function getIdentifier(): string
    {
        return 'deny_action';
    }

    public static function checkRights(int $userId, ?int$clientId, ?int $performerId): bool
    {
        return $userId === $performerId;
    }
}
