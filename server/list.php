<?php

$list = [];
$list[] = [
    'id' => 1231,
    'title' => 'yo',
    'completed' => 0
];
header('Content-Type: application/json');
echo json_encode($list);