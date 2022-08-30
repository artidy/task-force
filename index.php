<?php
require_once 'vendor/autoload.php';

use AndreyPechennikov\TaskForce\converter\CsvSqlConverter;

$converter = new CsvSqlConverter('data/csv');
$result = $converter->convertFiles('data/sql');

var_dump($result);
