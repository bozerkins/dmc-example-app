<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/bootstrap.php';

session_start();

$username = array_key_exists('username', $_REQUEST) ? $_REQUEST['username'] : null;
$password = array_key_exists('password', $_REQUEST) ? $_REQUEST['password'] : null;
$password_confirm = array_key_exists('password_confirm', $_REQUEST) ? $_REQUEST['password_confirm'] : null;

if ($username === null) {
    echo 'empty username';
    exit;
}
if ($password === null || empty($password)) {
    echo 'invalid passwords provided';
    exit;
}
if ($password !== $password_confirm) {
    $_SESSION['message_bad'] = 'Passwords does not match';
    header('Location: index.php');
    return;
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
    if ($user !== null) {
        $_SESSION['message_bad'] = 'Username already exists';
        header('Location: index.php');
        exit;
    }

    $user = [];
    $user['ID'] = getNextId('users');
    $user['Username'] = $username;
    $user['Password'] = password_hash($password, PASSWORD_BCRYPT);
    $user['CreatedAt'] = date('Y-m-d H:i:s');
    $users->create($user);

    $_SESSION['message_good'] = 'User created. You can login now!';
    header('Location: index.php');
    exit;

} catch (\Exception $ex) {
    echo 'Unexpected error';
    exit;
}