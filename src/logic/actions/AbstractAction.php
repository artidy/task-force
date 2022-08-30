<?php

namespace AndreyPechennikov\TaskForce\logic\actions;

abstract class AbstractAction
{
    abstract public static function getLabel(): string;

    abstract public static function getIdentifier(): string;

    abstract public static function checkRights(int $userId, ?int $clientId, ?int $performerId): bool;
}
