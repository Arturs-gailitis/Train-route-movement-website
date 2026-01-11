<?php 

require_once __DIR__.'/../db/initializeDB.php';
$database = __DIR__ . '/../storage/database/UserMessages.sqlite';

// izveido savienojumu ar datubāzi
$conection = getConnection($database);

// izdzēš veco Messages tabulu
$deleteTable = "DROP TABLE IF EXISTS Messages";
$conection->exec($deleteTable);

// izveido jaunu Messages tabulu
$createMessagesQuerry = "CREATE TABLE IF NOT EXISTS Messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL,
    message TEXT NOT NULL
);";
$conection->exec($createMessagesQuerry);

echo "Messages table created successfully\n";
?>