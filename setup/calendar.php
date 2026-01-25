<?php

require_once __DIR__.'/../db/initializeDB.php';

$database = __DIR__ . '/../storage/database/LatvianTrains.sqlite';
$info = __DIR__.'/../openInfo/calendar.csv';

$db = getConnection($database);

// izdzeš esošo Calendar tabulu
$deleteTable = 'DROP TABLE IF EXISTS Calendar';
$db->exec($deleteTable);

// sagatavo Calendar tabulas izveidošanas vaicājumu
$createCalendarQuerry = 'CREATE TABLE IF NOT EXISTS Calendar (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id TEXT UNIQUE,
    monday INTEGER,
    tuesday INTEGER,
    wednesday INTEGER,
    thursday INTEGER,
    friday INTEGER,
    saturday INTEGER,
    sunday INTEGER,
    start_date INTEGER,
    end_date INTEGER
)';

// izveido Calendar tabulu
$db->exec($createCalendarQuerry);

// fukcija kas ieliek trūkstošo Calendar ierakstu
function createMissingRecords($serviceId, $days, $startEndDates, $statement) {
    $statement->execute([
        'service_id' => $serviceId,
        'monday' => $days[0],
        'tuesday' => $days[1],
        'wednesday' => $days[2],
        'thursday' => $days[3],
        'friday' => $days[4],
        'saturday' => $days[5],
        'sunday' => $days[6],
        'start_date' => $startEndDates[0],
        'end_date' => $startEndDates[1],
    ]);
}

// sagatavo Calendar tabulas datu ievietošanas vaicājumu
$addCalendarQuerry = 'INSERT OR REPLACE INTO Calendar (service_id, monday, tuesday, wednesday, thursday, friday, saturday, 
    sunday, start_date, end_date) 
    VALUES 
(:service_id, :monday, :tuesday, :wednesday, :thursday, :friday, :saturday, :sunday, :start_date, :end_date)';
$temp = $db->prepare($addCalendarQuerry);

// ievieto calendar.csv datus iekšā Trips tabulā
try {
    $stream = fopen($info, "r");
    if ($stream) {
        fgetcsv($stream, 1000, ",");
        while (($csvData = fgetcsv($stream, 1000, ",")) !== false) {
            $temp->execute([
                'service_id' => $csvData[0],
                'monday' => $csvData[1],
                'tuesday' => $csvData[2],
                'wednesday' => $csvData[3],
                'thursday' => $csvData[4],
                'friday' => $csvData[5],
                'saturday' => $csvData[6],
                'sunday' => $csvData[7],
                'start_date' => $csvData[8],
                'end_date' => $csvData[9],
            ]);
        }
        fclose($stream);
    }

    // ieliek trūkstošo calendar ierastu
    createMissingRecords(806, [0, 0, 0, 0, 0, 1, 1], [20260101, 20261231], $temp);

} catch (Exception $e) {
    die('Kļūda apstrādājot datus: ' . $e->getMessage());
}

echo "Calendar table created successfully\n";

?>