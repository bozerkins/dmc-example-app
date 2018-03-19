<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use DataManagement\Model\EntityRelationship\Table;
use DataManagement\Model\TableHelper;

$table = new Table();
$table->addColumn('table', TableHelper::COLUMN_TYPE_STRING, 100);
$table->addColumn('last_id', TableHelper::COLUMN_TYPE_INTEGER);

$structure = var_export($table->structure(), true);
$location = __DIR__ . '/../../data/ids';

$instructions = <<<EOT
<?php
return [
    'location' => '{$location}',
    'structure' => {$structure}
];
EOT;
$instructionFileDestination = __DIR__ . '/../instructions/ids.php';
# create the file
touch($instructionFileDestination);
# make it writable by everyone
chmod($instructionFileDestination, 0777);
# write the contents
file_put_contents($instructionFileDestination, $instructions);

# create table
$table = Table::newFromInstructionsFile($instructionFileDestination);
$table->storage()->create();