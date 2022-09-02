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

    public static function checkRights(int $userId, ?int$clientId, ?int $performerId): bool
    {
        return $performerId === null;
    }
}
