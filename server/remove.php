<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/../bootstrap.php';

$user = initializeUserOrExit();

$id = array_key_exists('ID', $_REQUEST) ? (int) $_REQUEST['ID'] : null;

$todos = Table::newFromInstructionsFile(__DIR__ . '/../database/instructions/todos.php');
$todos->delete(function($record) use ($id, $user) {
    if ($record['ID'] === $id && $record['UserReference'] === $user['Reference']) {
        return Table::OPERATION_DELETE_INCLUDE_AND_STOP;
    }
});
sendOk([]);