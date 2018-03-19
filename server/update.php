<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/../bootstrap.php';

$user = initializeUserOrExit();

$id = array_key_exists('ID', $_REQUEST) ? (int) $_REQUEST['ID'] : null;
$title = array_key_exists('Title', $_REQUEST) ? $_REQUEST['Title'] : null;
$completed = array_key_exists('Completed', $_REQUEST) && $_REQUEST['Completed'] ? 1 : 0;

$todos = Table::newFromInstructionsFile(__DIR__ . '/../database/instructions/todos.php');
$result = $todos->read(function($record) use ($id, $user) {
    if ($record['ID'] === $id && $record['UserReference'] === $user['Reference']) {
        return Table::OPERATION_READ_INCLUDE_AND_STOP;
    }
});
$todo = null;
if ($result) {
    $todo = $result[0];
}
if ($todo === null) {
    sendFail('no such item by id');
    exit;
}

$change = ['Title' => $title, 'Completed' => $completed];

$todos->update(function($record) use ($id) {
    if ($record['ID'] === $id) {
        return Table::OPERATION_UPDATE_INCLUDE_AND_STOP;
    }
}, function() use ($change) { return $change; });

$todo = array_merge($todo, $change);

sendOk($todo);