<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use DataManagement\Model\EntityRelationship\Table;
use DataManagement\Model\TableHelper;

$table = new Table();
$table->addColumn('ID', TableHelper::COLUMN_TYPE_INTEGER);
$table->addColumn('Username', TableHelper::COLUMN_TYPE_STRING, 100);
$table->addColumn('Password', TableHelper::COLUMN_TYPE_STRING, 200 );
$table->addColumn('CreatedAt', TableHelper::COLUMN_TYPE_STRING, 20);

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

# create table
$table = Table::newFromInstructionsFile($instructionFileDestination);
$table->storage()->create();