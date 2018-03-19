<?php

use \DataManagement\Model\EntityRelationship\Table;

require_once __DIR__ . '/vendor/autoload.php';

session_start();

function getNextId(string $table)
{
    $ids = Table::newFromInstructionsFile(__DIR__ . '/database/instructions/ids.php');
    $ids->reserve(Table::RESERVE_READ_AND_WRITE);
    $result = $ids->read(function($record) use ($table) {
        if ($record['table'] === $table) {
            return Table::OPERATION_READ_INCLUDE_AND_STOP;
        }
    });
    $record = $result ? $result[0] : null;
    if ($record === null) {
        $ids->create($record = ['table' => $table, 'last_id' => 1]);
    } else {
        $ids->update(function($record) use ($table) {
            if ($record['table'] === $table) {
                return Table::OPERATION_UPDATE_INCLUDE_AND_STOP;
            }
        }, function($item) {
            return ['last_id' => $item['last_id'] + 1];
        });
        $record['last_id'] += 1;
    }
    $ids->release();

    return $record['last_id'];
}

/**
 * @return array|null
 * @throws Exception
 */
function initializeUser()
{
    if (array_key_exists('user_id', $_SESSION) === false) {
        return null;
    }

    $users = Table::newFromInstructionsFile(__DIR__ . '/database/instructions/users.php');
    $userId = $_SESSION['user_id'];
    $reference = null;
    $result = $users->read(function($record, \DataManagement\Model\EntityRelationship\TableIterator $iterator) use ($userId, &$reference) {
        if ($record['ID'] === $userId) {
            $reference = $iterator->position() - 1;
            return Table::OPERATION_READ_INCLUDE_AND_STOP;
        }
    });
    $user = null;
    if ($result) {
        $user = $result[0];
        $user['Reference'] = $reference;
    }
    return $user;
}

/**
 * @return array
 * @throws Exception
 */
function initializeUserOrExit()
{
    $user = initializeUser();
    if ($user === null) {
        sendFail('unauthorized access');
        exit;
    }
    return $user;
}

function sendOk(array $result)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => true, 'result' => $result]);
}

function sendFail(string $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'result' => $message]);
}