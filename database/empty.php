<?php
require_once 'bootsrap.php';

$tables = [
    'users','products',
    'carts','checkouts'
];

foreach ($tables as $key => $value)
{
    $conn->prepare("Truncate table " . $value)->execute();
}