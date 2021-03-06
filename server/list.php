<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/../bootstrap.php';

$user = initializeUserOrExit();

$todos = Table::newFromInstructionsFile(__DIR__ . '/../database/instructions/todos.php');
$result = $todos->read(function($record) use ($user) {
    if ($record['UserReference'] === $user['Reference']) {
        return Table::OPERATION_READ_INCLUDE;
    }
});

sendOk($result);