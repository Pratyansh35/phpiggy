<?php

declare(strict_types=1);

include __DIR__ . '/src/Framework/Database.php';

use Framework\Database;

$db = new Database($_ENV['DB_DRIVER'], [
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'],
    'dbname' => $_ENV['DB_NAME'],
], $_ENV['DB_USER'], $_ENV['DB_PASS']);

$sql_file = file_get_contents("./database.sql");

$db->connection->query($sql_file);

echo "Database created successfully\n";