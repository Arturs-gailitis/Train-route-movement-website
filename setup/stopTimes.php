<?php

require_once __DIR__.'/../db/initializeDB.php';

$database = __DIR__ . '/../storage/database/trains.sqlite';
$info = __DIR__.'/../open info/stop_times.csv';

$db = getConnection($database);

// izdzeš esošo Stop_Times tabulu
$deleteTable = 'DROP TABLE IF EXISTS Stop_Times';
$db->exec($deleteTable);

// sagatavo Stop_Times tabulas izveidošanas vaicājumu
$createSTQuerry = 'CREATE TABLE IF NOT EXISTS Stop_Times (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    trip_id INTEGER,
    arrival_time TEXT,
    departure_time TEXT,
    stop_id INTEGER,
    stop_sequence INTEGER,
    FOREIGN KEY (trip_id) REFERENCES Trips(trip_id),
    FOREIGN KEY (stop_id) REFERENCES Stops(stop_id)
)';

// izveido Stop_Times tabulu
$db->exec($createSTQuerry);

// sagatavo Stop_Times tabulas datu ievietošanas vaicājumu
$addSTQuerry = 'INSERT OR REPLACE INTO Stop_Times (trip_id, arrival_time, departure_time, stop_id, stop_sequence) 
    VALUES 
(:trip_id, :arrival_time, :departure_time, :stop_id, :stop_sequence)';
$temp = $db->prepare($addSTQuerry);

// ievieto stop_times.csv datus iekšā Stops tabulā
try {

    $stream = fopen($info, "r");
    if ($stream) {
        fgetcsv($stream, 1000, ",");
        while (($csvData = fgetcsv($stream, 1000, ",")) !== false) {
            $temp->execute([
                'trip_id' => $csvData[0],
                'arrival_time' => $csvData[1],
                'departure_time' => $csvData[2],
                'stop_id' => $csvData[3],
                'stop_sequence' => $csvData[4],
            ]);
        }
        fclose($stream);
    }

} catch (Exception $e) {
    die('Kļūda apstrādājot datus: ' . $e->getMessage());
}

echo "Stop_Times table created successfully\n";

?>