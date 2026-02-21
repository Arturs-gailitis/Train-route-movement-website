<?php 
// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/getingTrainRoute.php';
require_once __DIR__ . '/../../db/initializeDB.php';
$database = __DIR__ . '/../../storage/database/LatvianTrains.sqlite';
$firstIndex = null;
$lastIndex = null;
$list = [];
$orderedList = [];

// automātiski leitotāju aizmet uz sakumlapa.php ja nav iegājis savā profilā
if (isset($_SESSION['lietotajvards']) == false) {
    header("Location: sakumlapa.php");
    exit;
}

// automātiski leitotāju aizmet uz sakumlapa.php ja nav visi vajadzīgie dati
if (isset($_GET['sakumstacija']) == false || isset($_GET['beigustacija']) == false || isset($_GET['datums']) == false || 
isset($_GET['id']) == false || isset($_GET['marsruts']) == false) {
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

        // iegūst datus no marsruti.php
        $tripId = $_GET['id'];
        $firstStation = $_GET['sakumstacija'];
        $lastStation = $_GET['beigustacija'];
        $date = $_GET['datums'];
        $route = $_GET['marsruts'];

        // iegūst trip ierakstu Trips datubāzes tabulā
        $trip = getTrips($connection, $tripId);

        // skatās kādā virzienā maršruts ir
        if (str_contains($route, "Rīga -") == true) {
            $order = true;
        } else {
            $order = false;
        }

        // iegūst apstāšanās informāciju no Stop_Time datubāzes tabulas
        $stopTimes = getingStopTime($connection, $trip, $order);

        // iegūst konkrētu pieturu un to savieno ar pārējo saistošo informāciju
        foreach($stopTimes as $time) {
            $station = getingStations($connection, $time);

            $corectArrivalTime = "";
            $corectDepartureTime = "";
            $oldHour = "23";

            // Pārvērš stacijas ierašanās laiku pareizā stundu formātā
            if (substr($time['arrival_time'], 0, 2) == "25") {
                $minutesAndSeconds = substr($time['arrival_time'], 2);
                $t = $oldHour . $minutesAndSeconds;
                $corectArrivalTime = date("H:i:s", strtotime("+2 hours", strtotime($t)));
            } else if (substr($time['arrival_time'], 0, 2) == "24") {
                $minutesAndSeconds = substr($time['arrival_time'], 2);
                $t = $oldHour . $minutesAndSeconds;
                $corectArrivalTime = date("H:i:s", strtotime("+1 hours", strtotime($t)));
            } else {
                $corectArrivalTime = $time['arrival_time'];
            }

            // Pārvērš stacijas izbraukšanas laiku pareizā stundu formātā
            if (substr($time['departure_time'], 0, 2) == "25") {
                $minutesAndSeconds = substr($time['departure_time'], 2);
                $t = $oldHour . $minutesAndSeconds;
                $corectDepartureTime = date("H:i:s", strtotime("+2 hours", strtotime($t)));
            } else if (substr($time['departure_time'], 0, 2) == "24") {
                $minutesAndSeconds = substr($time['departure_time'], 2);
                $t = $oldHour . $minutesAndSeconds;
                $corectDepartureTime = date("H:i:s", strtotime("+1 hours", strtotime($t)));
            } else {
                $corectDepartureTime = $time['departure_time'];
            }
            
            $list[] = [
                "station" => $station['name'],
                "arrival" => $corectArrivalTime,
                "departute" => $corectDepartureTime,
            ];

        }

        // Pārbauda vai no augšas vai arī no apakšas būs jānofiltrē ieraksti objektā
        foreach($list as $i => $l) {
            if ($l['station'] == $firstStation) {
                $firstIndex = $i;
            } else if ($l['station'] == $lastStation) {
                $lastIndex = $i;
            }
        }

        // nofiltrē ierakstus jaunā objektā
        if ($firstIndex > $lastIndex) {
            for ($i = $firstIndex; $i >= $lastIndex; $i--) {
                $orderedList[] = [
                    "station" => $list[$i]['station'],
                    "arrival" => $list[$i]['arrival'],
                    "departute" => $list[$i]['departute'],
                ];
            }
        } else {
            for ($i = $firstIndex; $i <= $lastIndex; $i++) {
                $orderedList[] = [
                    "station" => $list[$i]['station'],
                    "arrival" => $list[$i]['arrival'],
                    "departute" => $list[$i]['departute'],
                ];
            }
        }
    }

} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="eng">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvian Train Route Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/info.css">
    <link rel="stylesheet" href="/style/global.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="icon" type="image/svg+xml" href="/icons/website icons/websiteIconTab.svg">
</head>
<body>
    <div class="galvene">
        <div class="nosaukums">
            <img src="/icons/website icons/websiteIconLight.svg" alt="Portal logo" id="logo">
            <h3 id="portālaNosaukums">Latvian Train Route Portal</h3>
        </div>

        <nav>
            <ul class="nav nav-pills" id="pogas">
                <?php if (isset($_SESSION['tiesibas']) && $_SESSION['tiesibas'] == "administrators"): ?>
                    <li class="nav-item" id="datubaze">
                        <a class="nav-link" href="database.php">Database</a>
                    </li>
                <?php endif ?>
                <li class="nav-item">
                    <a class="nav-link" href="main.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notifications.php">Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                <li class="nav-item" title="Profile">
                    <button class="nav-link" id="lietotajs">
                        <?php if (isset($_SESSION['tiesibas']) && $_SESSION['tiesibas'] == "lietotajs"): ?>
                            <img src="/icons/account icons/user.svg" alt="User" class="lietotajaIcona">
                        <?php elseif (isset($_SESSION['tiesibas']) && $_SESSION['tiesibas'] == "administrators"): ?>
                            <img src="/icons/account icons/admin.svg" alt="Administrator" class="lietotajaIcona">
                        <?php else: ?>
                            <img src="/icons/account icons/noAccountLight.svg" alt="No user" class="lietotajaIcona">
                        <?php endif ?>
                    </button>
                </li>
                <li class="nav-item" title="Options">
                    <button class="nav-link"><img src="/icons/settings.svg" alt="Options" id="opcijas"></button>
                </li>
            </ul>
        </nav>
        <div id="profilaLaukums">
            <ul>
                <?php if (isset($_SESSION['lietotajvards']) == false): ?>
                    <li>
                        <a class = "profilaStatuss" href="login.php">Log in</a>
                    </li>
                    <li>
                        <a class = "profilaStatuss" href="register.php">Create account</a>
                    </li>
                <?php elseif (isset($_SESSION['lietotajvards'])): ?>
                    <li>
                        <a class = "profilaStatuss" id="iziesana" href="logout.php">Log out</a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
        <div id="opcijuLaukums">

            <div class="fonaIzmaiņas">
                <label for="fonaIzmaiņas">Change light -></label>
                <button type="button" id="fonaIzmaiņasPoga" class="btn btn-primary">
                    <img src="/icons/lightTheme.svg" alt="Options" id="themeIkona"></button>
            </div>

            <div class="fonaIzmaiņas" id="valodaIzmaiņas">
                <label for="valoda">Change language -></label>
                <select name="valoda" id="valoda">
                    <option value="http://localhost:8000/body/eng/moreInfo.php?id=<?php echo $_GET['id']?>&sakumstacija=<?php echo $_GET['sakumstacija']?>&beigustacija=<?php echo $_GET['beigustacija']?>&datums=<?php echo $_GET['datums']?>&marsruts=<?php echo $_GET['marsruts']?>&altstart=<?php echo $_GET['altstart']?>&altend=<?php echo $_GET['altend']?>">English</option>
                    <option value="http://localhost:8000/body/lv/info.php?id=<?php echo $_GET['id']?>&sakumstacija=<?php echo $_GET['sakumstacija']?>&beigustacija=<?php echo $_GET['beigustacija']?>&datums=<?php echo $_GET['datums']?>&marsruts=<?php echo $_GET['marsruts']?>&altstart=<?php echo $_GET['altstart']?>&altend=<?php echo $_GET['altend']?>">Latvian</option>
                </select>
            </div>
        </div>
    </div>
    <div>
        <div id="virsrakstaLauks">
            <button id="iziet" class="btn btn-primary">
                Go back
            </button>
            <h1 id="infoNosaukums">More information</h1>
        </div>
        <div id="info">
            <img src="/icons/train-station.svg" alt="Start station" class="ikona" title="Start station">
            <span id="sakumaStacija"><?php echo $firstStation ?></span>
            <img src="/icons/train-station.svg" alt="End station" class="ikona" title="End station">
            <span id=beiguStacija><?php echo $lastStation ?></span>
            <img src="/icons/date.svg" alt="Date" class="ikona" title="Date">
            <span id=datums><?php echo $date ?></span>
        </div>
    </div>
    <div id="tabulaUnMape">
        <div id="Tabulas">
            <h2 id="tabulasTituls">Train arrivals at stations</h2>
            <table id="tabula">
                <thead>
                    <tr>
                        <th class=kollonuNosaukums><label>Station</label></th>
                        <th class=kollonuNosaukums><label>Arrival time</label></th>
                        <th class=kollonuNosaukums><label>Departure time</label></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderedList as $ol): ?>
                    <tr>
                        <td class="vertibas stacijas"><?= $ol['station'] ?></td>
                        <td class="vertibas"><?= $ol['arrival'] ?></td>
                        <td class="vertibas"><?= $ol['departute'] ?></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <h2 id="kartesTituls">Visual map</h2>
        <div id="karte"></div>
    </div>
</body>
<footer class="mt-5 py-3" id=footer>
    <p class="mb-0">© Latvian Train Route Portal <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Data used: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv </a> <br> Loaded: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/javascript/global.js"></script>
<script type="module" src="/javascript/info.js"></script>
</html>