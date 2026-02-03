<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/getingTrainRoute.php';
require_once __DIR__ . '/../../db/initializeDB.php';
$database = __DIR__ . '/../../storage/database/LatvianTrains.sqlite';
$trips = [];
$organisedTrips = [];

// automātiski leitotāju aizmet uz sakumlapa.php ja nav visi vajadzīgie dati
if (isset($_GET['sākumstacija']) == false || isset($_GET['beigustacija']) == false || isset($_GET['datums']) == false) {
    header("Location: sakumlapa.php");
    exit;
}

// izveido savienojumu ar datubāzi
try {
    $connection = getConnection($database);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        // Iegūst un apstrādā datus no sakumlapa.php formas
        $st = $_GET['sākumstacija'];
        $b = $_GET['beigustacija'];
        $date = $_GET['datums'];
        $startS = trim($st);
        $endS = trim($b);
        $dayName = strtolower(date('l', strtotime($date))); 

        // iegūst un apstrādā datus no Stops tabulas
        $startStation = getStops($connection, $startS);
        $endStation = getStops($connection, $endS);

        // Darbosies ja ir atrodamas abas stacijas
        if ($startStation != false && $endStation != false) {

            $startStopID = $startStation['stop_id'];
            $endStopID = $endStation['stop_id'];

            // iegūst un apstrādā datus no Stop_Times tabulas
            $startStationTime = getStopTime($connection, $startStopID);
            $endStationTime = getStopTime($connection, $endStopID);
            $TripID = [];
            $validTrips = [];

            // atrod visa sākumstaciju trip_id
            foreach ($startStationTime as $i => $time) {
                $TripID[$i] = $time['trip_id'];
            }

            // validē vai beigu staciju trip_id ir vienāds sākuma stacijas trip_id
            foreach ($endStationTime as $time) {
                $j = 0;
                for ($j = 0; $j < count($TripID); $j++) {
                    if ($time['trip_id'] == $TripID[$j]) {
                        $validTrips[] = $time['trip_id'];
                    }   
                }
            }

            // Ja trip_id nav nekur vienāds bet tās stacijas eksistē 
            if (empty($validTrips)) {

                // Iegūst visas Stop_Times no Rīgas stacijas ieraksta Stops tabulā
                $rigaStation = getStops($connection, "Rīga");
                $rigasStopId = $rigaStation['stop_id'];
                $rigaStopTimes = getStopTime($connection, $rigasStopId);

                $tempTrips = [];

                // Validē kuras Rīgas trip_id atbilst ar sākuma staciju
                foreach ($startStationTime as $s) {
                    foreach ($rigaStopTimes as $r) {
                        if ($s['trip_id'] == $r['trip_id'] && $s['stop_sequence'] < $r['stop_sequence']) {
                            $tempTrips[] = [
                                'start/end' => $s,
                                'Riga' => $r
                            ];
                        }
                    }
                }

                // Validē kuras Rīgas trip_id atbilst ar beiga staciju
                foreach ($endStationTime as $e) {
                    foreach ($rigaStopTimes as $r) {
                        if ($e['trip_id'] == $r['trip_id'] && $e['stop_sequence'] > $r['stop_sequence']) {
                            $tempTrips[] = [
                                'start/end' => $e,
                                'Riga' => $r
                            ];
                        }
                    }
                }

                foreach($tempTrips as $t) {

                    // iegūst datus no Trips, Routes un Calendar tabulas
                    $trip = getTrips($connection, $t["start/end"]['trip_id']);
                    $route = getRoutes($connection, $trip['route_id']);
                    $calendar = getCalendar($connection, $trip['service_id'], $dayName);

                    $startTime = [];
                    $endTime = [];
                    $startSt = "";
                    $endSt = "";

                    // Nosaka kura ir sākuma stacija un kura ir beigu stacija 
                    if ($t["start/end"]['stop_sequence'] < $t["Riga"]['stop_sequence']) {
                        $startTime = $t["start/end"];
                        $endTime = $t["Riga"];
                        $startSt = $st;
                        $endSt = "Rīga";
                    } else {
                        $startTime = $t["Riga"];
                        $endTime = $t["start/end"];
                        $startSt = "Rīga";
                        $endSt = $b;
                    }

                    $arrivalTime = null;
                    $departureTime = null;

                    // Pārvērš stacijas ierašanās laiku pareizā stundu formātā
                    if (substr($endTime['arrival_time'], 0, 2) == "25") {
                        $oldHour = "23";
                        $minutesAndSeconds = substr($endTime['arrival_time'], 2);
                        $t = $oldHour . $minutesAndSeconds;
                        $arrivalTime = strtotime("+2 hours", strtotime($t));
                    } else {
                        $arrivalTime = strtotime($endTime['arrival_time']);
                    }

                    // Pārvērš stacijas izbraukšanas laiku pareizā stundu formātā
                    if (substr($startTime['departure_time'], 0, 2) == "25") {
                        $oldHour = "23";
                        $minutesAndSeconds = substr($startTime['departure_time'], 2);
                        $t = $oldHour . $minutesAndSeconds;
                        $departureTime = strtotime("+2 hours", strtotime($t));
                    } else {
                        $departureTime = strtotime($startTime['departure_time']);
                    }

                    $tripTime = "";

                    // aprēķina dotās kustības laika posmu
                    $seconds = (($arrivalTime - $departureTime) % 60);
                    $minutes = floor((($arrivalTime - $departureTime) % 3600) / 60);
                    $hours = floor((($arrivalTime - $departureTime) / 3600));
                
                    $tripTime = $hours . ' h ' . $minutes . ' min ' . $seconds . ' s';

                    // iegūst pašreizējo laiku un datumu
                    date_default_timezone_set('Europe/Riga');
                    $currentTime = date('H:i:s');
                    $currentDate = date('Y-m-d');

                    // ieliek datus organizētā masīvā
                    if (!empty($calendar) && ($startTime['stop_sequence'] < $endTime['stop_sequence'])) {
                
                        // skatās vai izvēlētais datums vienāds/lielāks/mazāks par pašreizējo laiku
                        if ((strtotime($currentDate) == strtotime($date)) && 
                        (strtotime($startTime['departure_time']) > strtotime($currentTime))) {
                            $trips[] = [
                                'trip_id' => $trip['trip_id'],
                                'routeName' => $route['name'],
                                'startTime' => date("H:i:s", $departureTime),
                                'endTime' => date("H:i:s", $arrivalTime),
                                'tripTime' => $tripTime,
                                'startStation' => $startSt,
                                'endStation' => $endSt
                            ];
                        } else if (strtotime($currentDate) < strtotime($date)) {
                            $trips[] = [
                                'trip_id' => $trip['trip_id'],
                                'routeName' => $route['name'],
                                'startTime' => date("H:i:s", $departureTime),
                                'endTime' => date("H:i:s", $arrivalTime),
                                'tripTime' => $tripTime,
                                'startStation' => $startSt,
                                'endStation' => $endSt
                            ];
                        } else {
                            continue;
                        }
                    }
                }
            // Ja ir atrasti vienādi trip_id
            } else {

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

                    $arrivalTime = null;
                    $departureTime = null;

                    // Pārvērš stacijas ierašanās laiku pareizā stundu formātā
                    if (substr($endTime['arrival_time'], 0, 2) == "25") {
                        $oldHour = "23";
                        $minutesAndSeconds = substr($endTime['arrival_time'], 2);
                        $t = $oldHour . $minutesAndSeconds;
                        $arrivalTime = strtotime("+2 hours", strtotime($t));
                    } else {
                        $arrivalTime = strtotime($endTime['arrival_time']);
                    }

                    // Pārvērš stacijas izbraukšanas laiku pareizā stundu formātā
                    if (substr($startTime['departure_time'], 0, 2) == "25") {
                        $oldHour = "23";
                        $minutesAndSeconds = substr($startTime['departure_time'], 2);
                        $t = $oldHour . $minutesAndSeconds;
                        $departureTime = strtotime("+2 hours", strtotime($t));
                    } else {
                        $departureTime = strtotime($startTime['departure_time']);
                    }

                    $tripTime = "";

                    // aprēķina dotās kustības laika posmu
                    $seconds = (($arrivalTime - $departureTime) % 60);
                    $minutes = floor((($arrivalTime - $departureTime) % 3600) / 60);
                    $hours = floor((($arrivalTime - $departureTime) / 3600));
                    
                    $tripTime = $hours . ' h ' . $minutes . ' min ' . $seconds . ' s';

                    // iegūst pašreizējo laiku un datumu
                    date_default_timezone_set('Europe/Riga');
                    $currentTime = date('H:i:s');
                    $currentDate = date('Y-m-d');

                    // ieliek datus organizētā masīvā
                    if (!empty($calendar) && ($startTime['stop_sequence'] < $endTime['stop_sequence'])) {
                    
                        // skatās vai izvēlētais datums vienāds/lielāks/mazāks par pašreizējo laiku
                        if ((strtotime($currentDate) == strtotime($date)) && 
                        (strtotime($startTime['departure_time']) > strtotime($currentTime))) {
                            $trips[] = [
                                'trip_id' => $trip['trip_id'],
                                'routeName' => $route['name'],
                                'startTime' => date("H:i:s", $departureTime),
                                'endTime' => date("H:i:s", $arrivalTime),
                                'tripTime' => $tripTime,
                                'startStation' => $st,
                                'endStation' => $b
                            ];
                        } else if (strtotime($currentDate) < strtotime($date)) {
                            $trips[] = [
                                'trip_id' => $trip['trip_id'],
                                'routeName' => $route['name'],
                                'startTime' => date("H:i:s", $departureTime),
                                'endTime' => date("H:i:s", $arrivalTime),
                                'tripTime' => $tripTime,
                                'startStation' => $st,
                                'endStation' => $b
                            ];
                        } else {
                            continue;
                        }
                    }
                }
            }
            // organizē maršruta masīvu pār atiešana laika
            $organisedTrips = organiseArray($trips);
        }
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
                <?php if (isset($_SESSION['tiesibas']) && $_SESSION['tiesibas'] == "administrators"): ?>
                    <li class="nav-item" id="datubaze">
                        <a class="nav-link" href="datubaze.php">Datubāze</a>
                    </li>
                <?php endif ?>
                <li class="nav-item">
                    <a class="nav-link" href="sakumlapa.php">Sākumlapa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Paziņojumi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kontakti.php">Kontakti</a>
                </li>
                <li class="nav-item" title="Profils">
                    <button class="nav-link" id="lietotajs">
                        <?php if (isset($_SESSION['tiesibas']) && $_SESSION['tiesibas'] == "lietotajs"): ?>
                            <img src="/icons/account icons/user.svg" alt="Lietotājs" class="lietotajaIcona">
                        <?php elseif (isset($_SESSION['tiesibas']) && $_SESSION['tiesibas'] == "administrators"): ?>
                            <img src="/icons/account icons/admin.svg" alt="Administrators" class="lietotajaIcona">
                        <?php else: ?>
                            <img src="/icons/account icons/noAccountLight.svg" alt="Bez lietotāja" class="lietotajaIcona">
                        <?php endif ?>
                    </button>
                </li>
                <li class="nav-item" title="Opcijas">
                    <button class="nav-link"><img src="/icons/settings.svg" alt="Opcijas" id="opcijas"></button>
                </li>
            </ul>
        </nav>
        <div id="profilaLaukums">
            <ul>
                <?php if (isset($_SESSION['lietotajvards']) == false): ?>
                    <li>
                        <a class = "profilaStatuss" href="pieteikties.php">Pieslēdzies savā kontā</a>
                    </li>
                    <li>
                        <a class = "profilaStatuss" href="registracija.php">Izveido jaunu kontu</a>
                    </li>
                <?php elseif (isset($_SESSION['lietotajvards'])): ?>
                    <li>
                        <a class = "profilaStatuss" id="iziesana" href="iziet.php">Iziet ārā no sava konta</a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
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
    <div id="paradītMeklēšanasSadaļu">
        <button type="button" id="atvērtMeklšanuPoga" title="Atvērt meklēšanas sadaļu">
            <img src="/icons/arrow-down.svg" alt="Atvērt meklēšanas sadaļu" id="atvērtmeklēšanuIkona">
        </button>
    </div>
    <div class= "p-2 mx-4" id="maršrutuMeklēšanasSadaļa">
        <button type="button" class="btn btn-secondary" id="atcelt" title="Aizvērt meklēšanas sadaļu">
            <img src="/icons/cross.svg" alt="Atcelt" id="atceltIcona">
        </button>

        <h2 class="mb-4" id="meklesanasTituls">Meklēšana</h2>
        <form id="meklesanasForma" method="get">
            <div class="mb-3">
                <label for="sākumstacija">Sākuma stacija:</label>
                <input type="text" class="form-control" name="sākumstacija" id="sākumstacija" value= "<?php echo $st ?>" required>
            </div>

            <div class="mb-3">
                <button id=apgriezt class="btn btn-primary" type="button">
                    <img src="/icons/reverse.svg" alt="Apgriezt" id=apgireztIcona>
                </button>
            </div>

            <div class="mb-3">
                <label for="beigustacija">Beigu stacija:</label>
                <input type="text" class="form-control" name="beigustacija" id="beigustacija" value= "<?php echo $b ?>" required>
            </div>

            <div class="mb-3">
                <label for="datums">Datums:</label>
                <input type="date" class="form-control" name="datums" id="datums" value= "<?php echo $date ?>" required>
            </div>

            <input type="submit" value="Meklēt" class="btn btn-primary" id ="meklet">
        </form>
    </div>
    <div id="tabulasNosaukums">
        <h1 id="marsrutuTabulasNosaukums">Vilcienu kustības grafiks</h1>
        <div id="virsInfo">
            <img src="/icons/train-station.svg" alt="Sākuma stacija" class="ikona" title="Sākuma stacija">
            <span id="sakumaStacija"><?php echo $st ?></span>
            <img src="/icons/train-station.svg" alt="Beigu stacija" class="ikona" title="Beigu stacija">
            <span id=beiguStacija><?php echo $b ?></span>
            <img src="/icons/date.svg" alt="Datums" class="ikona" title="Datums">
            <span id=datumaInfo><?php echo $date ?></span>
        </div>
    </div>
    <?php if ($startStation == false || $endStation == false): ?>
        <div class="kluduZinojums">
            <img src="/icons/error.svg" alt="Klūdas zīme" class="kluda">
            <p class="kluduTeksts">
                Viena no ievadītās stacijām <b><?php echo $st ?> </b> un <b> <?php echo $b ?> </b> 
                nav pieejama informācija vai arī tās neeksistē. <br> Pārbaudiet vai neesat uzrakstījuši nepareizi 
                stacijas nosaukumu un mēģiniet vēlreiz.
            </p>
        </div>
    <?php elseif (empty($organisedTrips) == true): ?>
        <div class="kluduZinojums">
            <img src="/icons/error.svg" alt="Klūdas zīme" class="kluda">
            <p class="kluduTeksts">
                Jūsu izvēlētajā dienā vairs nav pieejami maršruti no <b><?php echo $st ?> </b> uz <b> <?php echo $b ?> </b> <br>
                Mēģiniet izvēlēties citas dienas. Piemēram, nākošajā dienā vai pēc nedēļas utt.
            </p>
        </div>
    <?php else: ?>
        <div id="marsrutuTabulasLaukums">
            <table id="marsrutuTabula">
                <thead>
                    <tr>
                        <th class="marsruti" id="sakums"><label id="sakumaTeksts">Atiešanas laiks</label></th>
                        <th class="marsruti" id="beigas"><label id="beigasTeksts">Pienākšanas laiks</label></th>
                        <th class="marsruti" id="nosaukums"><label id="nosaukumaTeksts">Maršruta nosaukums</label></th>
                        <th class="marsruti" id="marsrutaIdentifikators">
                            <label id="identifikatorsTeksts">Maršruta nr.</label>
                        </th>
                        <th class="marsruti" id="laiks"><label id="laikaTeksts">Maršruta laiks</label></th>
                        <th class="marsruti" id="pirkt"><label id="pirktTeksts">Pirkt biļeti</label></th>
                        <th class="marsruti" id="info"><label id="infoTeksts">Vairāk info</label></th>
                        <th id="statuss"><label id="statusaTeksts">Statuss</label></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($organisedTrips as $trip) : ?>
                        <tr>
                            <td id="sakumaLaiks"><?= $trip['startTime'] ?></td>
                            <td id="beigasLaiks"><?= $trip['endTime'] ?></td>
                            <td id="marsrutaNosaukums"><?= $trip['routeName'] ?></td>
                            <td id="identifikators"><?= $trip['trip_id'] ?></td>
                            <td id="marsrutaLaiks"><?= $trip['tripTime'] ?></td>
                            <?php if (isset($_SESSION['lietotajvards'])): ?>
                                <td id="pirktPoga">
                                    <a class=pirktPogas href="">
                                        <img src="/icons/buy.svg" alt="Pirkt" id="pirktIkona">
                                    </a>
                                </td>
                                <td id="infoPoga"><a class="infoPogas" 
                                href="info.php?id=<?php echo $trip['trip_id'] ?>&sakumstacija=<?php echo $trip['startStation'] ?>&beigustacija=<?php echo $trip['endStation'] ?>&datums=<?php echo $date ?>&marsruts=<?php echo $trip['routeName'] ?>">
                                    <img src="/icons/info.svg" alt="Vairāk info" id="infoIkona"></a>
                                </td>
                            <?php else: ?>
                                <td id="pirktPoga">
                                    <img src="/icons/buy.svg" alt="Pirkt" id="pirktIkona" title="Tikai reģistrētiem lietotājiem">
                                </td>
                                <td id="infoPoga">
                                    <img src="/icons/info.svg" alt="Vairāk info" id="infoIkona" title="Tikai reģistrētiem lietotājiem">
                                </td>
                            <?php endif ?>
                            <td id="statussLaukums"> </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
<footer class="mt-5 py-3">
    <p class="mb-0">© Latvijas vilcienu maršrutu kustības portāls <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Izmantotie dati: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv </a> <br> Ielādēts: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="/javascript/global.js"></script>
<script type="module" src="/javascript/marsruti.js"></script>
</html>