<?php

// this loads the settings and the connection helper
$config = require __DIR__ . '/../config.php';
require __DIR__ . '/../src/Database.php';

// connects to the sqlite file and it gets created if it doesn't exist yet
$db = Database::connect($config['db_path']);
// executes the schema.sql file to create the tables
$db->exec(file_get_contents(__DIR__ . '/../database/schema.sql'));

// prints a message to the console
echo "Database ready: {$config['db_path']}\n";