<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/bootstrap.php';

session_start();

$username = array_key_exists('username', $_REQUEST) ? $_REQUEST['username'] : null;
$password = array_key_exists('password', $_REQUEST) ? $_REQUEST['password'] : null;

if ($username === null) {
    echo 'empty username';
    exit;
}
try {
    $users = Table::newFromInstructionsFile(__DIR__ . '/database/instructions/users.php');
    $result = $users->read(function($record) use ($username, $password) {
        if ($record['Username'] === $username && password_verify($password,$record['Password'])) {
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
        exit;
    }
    // set user
    $_SESSION['user_id'] = $user['ID'];
    // redirect
    header('Location: app/index.php');
    exit;
} catch (\Exception $ex) {
    echo 'Unexpected error';
    exit;
}