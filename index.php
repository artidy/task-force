<?php
require_once 'vendor/autoload.php';

use AndreyPechennikov\TaskForce\logic\AvailableActions;
use AndreyPechennikov\TaskForce\logic\actions\CancelAction;

$strategy = new AvailableActions(AvailableActions::STATUS_IN_PROGRESS, 5, 1);
print($strategy->getNextStatus(CancelAction::getIdentifier()) == AvailableActions::STATUS_CANCEL);
