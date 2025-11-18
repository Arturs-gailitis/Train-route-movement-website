<?php

require_once __DIR__.'/../db/initializeDB.php';

$database = __DIR__ . '/../storage/database/trains.sqlite';
$info = __DIR__.'/../open info/routes.csv';

$db = getConnection($database);

// izdzeš esošo Routes tabulu
$deleteTable = 'DROP TABLE IF EXISTS Routes';
$db->exec($deleteTable);

// sagatavo Trips tabulas izveidošanas vaicājumu
$createRouteQuerry = 'CREATE TABLE IF NOT EXISTS Routes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    route_id INTEGER UNIQUE,
    agency TEXT,
    name TEXT,
    type INTEGER,
    color TEXT,
    text_color TEXT
)';

// izveido Route tabulu
$db->exec($createRouteQuerry);

// sagatavo Route tabulas datu ievietošanas vaicājumu
$addRouteQuerry = 'INSERT OR REPLACE INTO Routes (route_id, agency, name, type, color, text_color)
    VALUES (:route_id, :agency, :name, :type, :color, :text_color)';
$temp = $db->prepare($addRouteQuerry);

// ievieto routes.csv datus iekšā Route tabulā
try {
    $stream = fopen($info, "r");
    if ($stream) {
        fgetcsv($stream, 1000, ",");
        while (($csvData = fgetcsv($stream, 1000, ",")) !== false) {

            $temp->execute([
                'route_id' => $csvData[0],
                'agency' => $csvData[1],
                'name' => $csvData[3],
                'type' => $csvData[5],
                'color' => $csvData[7],
                'text_color' => $csvData[8]
            ]);
        }
        fclose($stream);
    }
} catch (Exception $e) {
    die('Kļūda apstrādājot datus: ' . $e->getMessage());
}

echo "Routes table created successfully\n";

?>