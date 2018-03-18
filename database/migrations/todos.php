<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use DataManagement\Model\EntityRelationship\Table;
use DataManagement\Model\TableHelper;

$table = new Table();
$table->addColumn('ID', TableHelper::COLUMN_TYPE_INTEGER);
$table->addColumn('Title', TableHelper::COLUMN_TYPE_STRING, 255);
$table->addColumn('Completed', TableHelper::COLUMN_TYPE_INTEGER);
$table->addColumn('UserReference', TableHelper::COLUMN_TYPE_INTEGER);

$structure = var_export($table->structure(), true);
$location = __DIR__ . '/../../data/todos';

$instructions = <<<EOT
<?php
return [
    'location' => '{$location}',
    'structure' => {$structure}
];
EOT;
$instructionFileDestination = __DIR__ . '/../instructions/todos.php';
# create the file
touch($instructionFileDestination);
# make it writable by everyone
chmod($instructionFileDestination, 0777);
# write the contents
file_put_contents($instructionFileDestination, $instructions);