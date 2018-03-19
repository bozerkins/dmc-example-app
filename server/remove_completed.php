<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/../bootstrap.php';

$user = initializeUserOrExit();

$todos = Table::newFromInstructionsFile(__DIR__ . '/../database/instructions/todos.php');

$todos->delete(function($record) use ($user) {
    if ($record['UserReference'] === $user['Reference'] && $record['Completed'] === 1) {
        return Table::OPERATION_UPDATE_INCLUDE;
    }
});

sendOk([]);