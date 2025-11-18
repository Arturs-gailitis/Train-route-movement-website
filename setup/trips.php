<?php

require_once __DIR__.'/../db/initializeDB.php';

$database = __DIR__ . '/../storage/database/trains.sqlite';
$info = __DIR__.'/../open info/trips.csv';

$db = getConnection($database);

// izdzeš esošo Trips tabulu
$deleteTable = 'DROP TABLE IF EXISTS Trips';
$db->exec($deleteTable);

// sagatavo Trips tabulas izveidošanas vaicājumu
$createTripsQuerry = 'CREATE TABLE IF NOT EXISTS Trips (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    route_id INTEGER,
    service_id TEXT,
    trip_id INTEGER UNIQUE,
    headsign TEXT,
    FOREIGN KEY (route_id) REFERENCES Routes(route_id)
)';

// izveido Trips tabulu
$db->exec($createTripsQuerry);

// sagatavo Trips tabulas datu ievietošanas vaicājumu
$addTripsQuerry = 'INSERT OR REPLACE INTO Trips (route_id, service_id, trip_id, headsign) 
    VALUES 
(:route_id, :service_id, :trip_id, :headsign)';
$temp = $db->prepare($addTripsQuerry);

// ievieto trips.csv datus iekšā Trips tabulā
try {

    $stream = fopen($info, "r");
    if ($stream) {
        fgetcsv($stream, 1000, ",");
        while (($csvData = fgetcsv($stream, 1000, ",")) !== false) {
            $temp->execute([
                'route_id' => $csvData[0],
                'service_id' => $csvData[1],
                'trip_id' => $csvData[2],
                'headsign' => $csvData[3],
            ]);
        }
        fclose($stream);
    }

} catch (Exception $e) {
    die('Kļūda apstrādājot datus: ' . $e->getMessage());
}

echo "Trips table created successfully\n";

?>