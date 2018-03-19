<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/bootstrap.php';

session_start();

unset($_SESSION['user_id']);
header('Location: index.php');
exit;