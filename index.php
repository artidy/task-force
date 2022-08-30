<?php
require_once 'vendor/autoload.php';

use AndreyPechennikov\TaskForce\logic\AvailableActions;

$strategy = new AvailableActions(AvailableActions::STATUS_IN_PROGRESS, 5, 1);
print(assert($strategy->getNextStatus(AvailableActions::ACTION_CANCEL) == AvailableActions::STATUS_CANCEL, 'Операция отмены'));
