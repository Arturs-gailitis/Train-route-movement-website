<?php

require_once __DIR__.'/../db/initializeDB.php';

$database = __DIR__ . '/../storage/database/trains.sqlite';
$info = __DIR__.'/../open info/stops.csv';

$db = getConnection($database);

// izdzeš esošo Stops tabulu
$deleteTable = 'DROP TABLE IF EXISTS Stops';
$db->exec($deleteTable);

// sagatavo Stops tabulas izveidošanas vaicājumu
$createStopsQuerry = 'CREATE TABLE IF NOT EXISTS Stops (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    stop_id INTEGER UNIQUE,
    name TEXT,
    latitude REAL,
    longitude REAL
)';

// izveido Stops tabulu
$db->exec($createStopsQuerry);

// sagatavo Stops tabulas datu ievietošanas vaicājumu
$addStopsQuerry = 'INSERT OR REPLACE INTO Stops (stop_id, name, latitude, longitude) 
    VALUES 
(:stop_id, :name, :latitude, :longitude)';
$temp = $db->prepare($addStopsQuerry);

// ievieto stops.csv datus iekšā Stops tabulā
try {

    $stream = fopen($info, "r");
    if ($stream) {
        fgetcsv($stream, 1000, ",");
        while (($csvData = fgetcsv($stream, 1000, ",")) !== false) {
            $temp->execute([
                'stop_id' => $csvData[0],
                'name' => $csvData[2],
                'latitude' => $csvData[4],
                'longitude' => $csvData[5],
            ]);
        }
        fclose($stream);
    }

} catch (Exception $e) {
    die('Kļūda apstrādājot datus: ' . $e->getMessage());
}

echo "Stops table created successfully\n";

?>