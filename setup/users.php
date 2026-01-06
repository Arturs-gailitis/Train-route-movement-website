<?php

require_once __DIR__.'/../db/initializeDB.php';
$database = __DIR__ . '/../storage/database/Users.sqlite';

// izveido savienojumu ar datubāzi
$conection = getConnection($database);

// izdzēš veco Users tabulu
$deleteTable = "DROP TABLE IF EXISTS Users";
$conection->exec($deleteTable);

// izveido jaunu Usrs tabulu
$createUsersQuerry = "CREATE TABLE IF NOT EXISTS Users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    rights TEXT NOT NULL
        CHECK (rights IN ('lietotajs', 'administrators')),
    password TEXT NOT NULL
)";
$conection->exec($createUsersQuerry);

// ievieto noklusēto lietotāju Users tabulā
$addUserQuerry = "INSERT INTO Users (username, email, rights, password) 
    VALUES 
(:username, :email, :rights, :password)";
$temp = $conection->prepare($addUserQuerry);

$temp->execute([
    "username" => "Adminis342",
    "email" => "adminis342@test.com",
    "rights" => "administrators",
    "password" => password_hash("helloWorld431", PASSWORD_DEFAULT),
]);

echo "Users table created successfully\n";
?>