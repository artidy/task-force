<?php

namespace AndreyPechennikov\TaskForce\logic\actions;

class ResponseAction extends AbstractAction
{
    public static function getLabel(): string
    {
        return 'Ответить';
    }

    public static function getIdentifier(): string
    {
        return 'response_action';
    }

    public static function checkRights($userId, $performerId, $clientId): bool
    {
        return $userId === $performerId;
    }
}
