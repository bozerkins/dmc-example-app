<?php

session_start();

if (array_key_exists('user_id', $_SESSION)) {
//    include __DIR__ . '/templates/already_logged_in.php';
    echo 'already logged in';
    exit;
}

include __DIR__ . '/templates/index.php';