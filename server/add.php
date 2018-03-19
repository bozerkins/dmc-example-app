<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/../bootstrap.php';

$user = initializeUserOrExit();

$title = array_key_exists('Title', $_REQUEST) ? $_REQUEST['Title'] : null;
$completed = array_key_exists('Completed', $_REQUEST) && $_REQUEST['Completed'] ? 1 : 0;

$todos = Table::newFromInstructionsFile(__DIR__ . '/../database/instructions/todos.php');
$record = [];
$record['ID'] = getNextId('todos');
$record['Title'] = $title;
$record['Completed'] = $completed;
$record['UserReference'] = $user['Reference'];

$todos->create($record);

sendOk($record);