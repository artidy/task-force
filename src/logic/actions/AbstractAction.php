<?php

namespace AndreyPechennikov\TaskForce\logic\actions;

abstract class AbstractAction
{
    abstract public static function getLabel();

    abstract public static function getIdentifier();

    abstract public static function checkRights($userId, $performerId, $clientId);
}
