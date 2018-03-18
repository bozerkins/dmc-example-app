<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/bootstrap.php';

session_start();

$username = array_key_exists('username', $_REQUEST) ? $_REQUEST['username'] : null;

if ($username === null) {
    echo 'empty username';
    exit;
}
try {
    $users = Table::newFromInstructionsFile(__DIR__ . '/database/instructions/users.php');
    $result = $users->read(function($record) use ($username) {
        if ($record['Username'] === $username) {
            return Table::OPERATION_READ_INCLUDE_AND_STOP;
        }
    });
    $user = null;
    if ($result) {
        $user = $result[0];
    }
    if ($user === null) {
        $_SESSION['message_bad'] = 'Authentication error';
        header('Location: index.php');
        return;
    }
    echo 'omg. user exists';exit;
} catch (\Exception $ex) {
    echo 'Unexpected error';
    exit;
}

print_r($_REQUEST);
exit;