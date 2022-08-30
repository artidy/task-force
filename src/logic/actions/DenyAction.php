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

    public static function checkRights($userId, $performerId, $clientId): bool
    {
        return $userId === $performerId;
    }
}
