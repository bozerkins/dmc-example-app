<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use DataManagement\Model\EntityRelationship\Table;
use DataManagement\Model\TableHelper;

$table = new Table();
$table->addColumn('ID', TableHelper::COLUMN_TYPE_INTEGER);
$table->addColumn('Username', TableHelper::COLUMN_TYPE_FLOAT);
$table->addColumn('Email', TableHelper::COLUMN_TYPE_INTEGER);
$table->addColumn('Password', TableHelper::COLUMN_TYPE_STRING, 40 );

$structure = var_export($table->structure(), true);
$location = __DIR__ . '/../../data/users';

$instructions = <<<EOT
<?php
return [
    'location' => '{$location}',
    'structure' => {$structure}
];
EOT;
$instructionFileDestination = __DIR__ . '/../instructions/users.php';
# create the file
touch($instructionFileDestination);
# make it writable by everyone
chmod($instructionFileDestination, 0777);
# write the contents
file_put_contents($instructionFileDestination, $instructions);