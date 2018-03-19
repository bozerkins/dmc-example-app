<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/../bootstrap.php';

$user = initializeUserOrExit();

$completed = array_key_exists('Completed', $_REQUEST) && $_REQUEST['Completed'] ? 1 : 0;

$todos = Table::newFromInstructionsFile(__DIR__ . '/../database/instructions/todos.php');

$todos->update(function($record) use ($user) {
    if ($record['UserReference'] === $user['Reference']) {
        return Table::OPERATION_UPDATE_INCLUDE;
    }
}, function($record) use ($completed) { return ['Completed' => (int) $completed]; });

sendOk([]);