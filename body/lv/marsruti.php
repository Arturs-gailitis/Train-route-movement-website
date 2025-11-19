<?php

require_once __DIR__ . '/../../db/getingTrainRoute.php';
require_once __DIR__ . '/../../db/initializeDB.php';
$database = __DIR__ . '/../../storage/database/trains.sqlite';
$trips = [];
$organisedTrips = [];

// izveido savienojumu ar datubāzi
try {
    $connection = getConnection($database);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Iegūst un apstrādā datus no sakumlapa.php formas
        $s = $_POST['sākumstacija'];
        $b = $_POST['beigustacija'];
        $date = $_POST['datums'];
        $startS = trim($s);
        $endS = trim($b);
        $dayName = strtolower(date('l', strtotime($date))); 

        // iegūst un apstrādā datus no Stops tabulas
        $startStation = getStops($connection, $startS);
        $endStation = getStops($connection, $endS);
        $startStopID = $startStation['stop_id'];
        $endStopID = $endStation['stop_id'];

        // iegūst un apstrādā datus no Stop_Times tabulas
        $startStationTime = getStopTime($connection, $startStopID);
        $endStationTime = getStopTime($connection, $endStopID);
        $TripID = [];
        $validTrips = [];
        foreach ($startStationTime as $i => $time) {
            $TripID[$i] = $time['trip_id'];
        }
        foreach ($endStationTime as $time) {
            $j = 0;
            for ($j = 0; $j < count($TripID); $j++) {
                if ($time['trip_id'] == $TripID[$j]) {
                    $validTrips[] = $time['trip_id'];
                }   
            }
        }


        foreach($validTrips as $tripID) {
            
            // iegūst datus no Trips, Routes un Calendar tabulas
            $trip = getTrips($connection, $tripID);
            $route = getRoutes($connection, $trip['route_id']);
            $calendar = getCalendar($connection, $trip['service_id'], $dayName);
            
            // atrod vienādu sākuma stacijas trip_id no $validTrips masīva 
            foreach ($startStationTime as $time) {
                if ($time['trip_id'] == $trip['trip_id']) {
                    $startTime = $time;
                    break;
                }
            }

            // atrod vienādu beigu stacijas trip_id no $validTrips masīva 
            foreach ($endStationTime as $time) {
                if ($time['trip_id'] == $trip['trip_id']) {
                    $endTime = $time;
                    break;
                }
            }

            // aprēķina dotās kustības laika posmu
            $seconds = ((strtotime($endTime['arrival_time']) - strtotime($startTime['departure_time'])) % 60);
            $minutes = floor(((strtotime($endTime['arrival_time']) - strtotime($startTime['departure_time'])) % 3600) / 60);
            $hours = floor(((strtotime($endTime['arrival_time']) - strtotime($startTime['departure_time'])) / 3600));
            $tripTime = $hours . ':' . $minutes . ':' . $seconds;

            // ieliek datus organizētā masīvā
            if (!empty($calendar) && ($startTime['stop_sequence'] < $endTime['stop_sequence'])) {
                $trips[] = [
                    'trip_id' => $trip['trip_id'],
                    'routeName' => $route['name'],
                    'startTime' => $startTime['departure_time'],
                    'endTime' => $endTime['arrival_time'],
                    'tripTime' => $tripTime,
                ];
            }
        }

        // organizē maršruta masīvu pēc atiešana laika
        $organisedTrips = organiseArray($trips);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvijas vilcienu maršrutu kustības portāls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/global.css">
    <link rel="stylesheet" href="/style/movement.css">
    <link rel="icon" type="image/svg+xml" href="/icons/website icons/websiteIconTab.svg">
</head>
<body>
    <div class="galvene">
        <div class="nosaukums">
            <img src="/icons/website icons/websiteIconLight.svg" alt="Portāla logo" id="logo">
            <h3 id="portālaNosaukums">Latvijas vilcienu maršrutu kustības portāls</h3>
        </div>

        <nav>
            <ul class="nav nav-pills" id="pogas">
                <li class="nav-item">
                    <a class="nav-link" href="#">Paziņojumi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Kontakti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><img src="/icons/shoppingCart.png" alt="Grozs" id="grozs"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><img src="/icons/account icons/noAccountLight.svg" alt="Lietotājs" 
                        id="lietotajs"></a>
                </li>
                <li class="nav-item">
                    <button class="nav-link"><img src="/icons/settings.svg" alt="Opcijas" id="opcijas"></button>
                </li>
            </ul>
        </nav>
        <div id="opcijuLaukums">

            <div class="fonaIzmaiņas">
                <label for="fonaIzmaiņas">Izmainīt fonu -></label>
                <button type="button" id="fonaIzmaiņasPoga" class="btn btn-primary">
                    <img src="/icons/lightTheme.svg" alt="Opcijas" id="themeIkona"></button>
            </div>

            <div class="fonaIzmaiņas" id="valodaIzmaiņas">
                <label for="valoda">Valodas maiņa -></label>
                <select name="valoda" id="valoda">
                    <option value="Latviešu">Latviešu</option>
                    <option value="Angļu">Angļu</option>
                </select>
            </div>

        </div>
    </div>
    <div id="marsrutuTabulasLaukums">
        <table id="marsrutuTabula">
            <thead>
                <tr>
                    <th>Atiešanas laiks</th>
                    <th>Ierašanās laiks</th>
                    <th>Maršruta nosaukums</th>
                    <th>Maršruta nummurs</th>
                    <th>Ceļa ilgums</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($organisedTrips as $trip) : ?>
                    <tr>
                        <td><?= $trip['startTime'] ?></td>
                        <td><?= $trip['endTime'] ?></td>
                        <td><?= $trip['routeName'] ?></td>
                        <td><?= $trip['trip_id'] ?></td>
                        <td><?= $trip['tripTime'] ?></td>
                        <td><button class="btn btn-primary" id="pirktPogas">Pirkt</button></td>
                        <td><button class="btn btn-primary" id="infoPogas">Vairāk info</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="/javascript/global.js"></script>
</body>
<footer class="mt-5 py-3">
    <p class="mb-0">© Latvijas vilcienu maršrutu kustības portāls 2025</p>
    <p class="mb-4" id="dati">
        Izmantotie dati: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv
        </a>
    </p>
</footer>