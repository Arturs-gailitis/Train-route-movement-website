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
            
            $list[] = [
                "station" => $station['name'],
                "arrival" => $time['arrival_time'],
                "departute" => $time['departure_time'],
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
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvijas vilcienu maršrutu kustības portāls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="/style/global.css">
    <link rel="stylesheet" href="/style/info.css">
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
                    <a class="nav-link" href="sakumlapa.php">Sākumlapa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Paziņojumi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Kontakti</a>
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
    <div>
        <h1 id="infoNosaukums">Vairāk informācijas</h1>
        <div id="info">
            <img src="/icons/train-station.svg" alt="Sākuma stacija" class="ikona" title="Sākuma stacija">
            <span id="sakumaStacija"><?php echo $firstStation ?></span>
            <img src="/icons/train-station.svg" alt="Beigu stacija" class="ikona" title="Beigu stacija">
            <span id=beiguStacija><?php echo $lastStation ?></span>
            <img src="/icons/date.svg" alt="Datums" class="ikona" title="Datums">
            <span id=datums><?php echo $date ?></span>
        </div>
    </div>
    <div id="tabulaUnMape">
        <div id="Tabulas">
            <table id="tabula">
                <thead>
                    <tr>
                        <th class=kollonuNosaukums><label>Stacija</label></th>
                        <th class=kollonuNosaukums><label>Atiešanas laiks</label></th>
                        <th class=kollonuNosaukums><label>Izbraukšanas laiks</label></th>
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
        <div id="karte"></div>
    </div>
    <button id="iziet">
        Iet atpakaļ uz maršruta tabulu
    </button>
</body>
<footer class="mt-5 py-3" id=footer>
    <p class="mb-0">© Latvijas vilcienu maršrutu kustības portāls <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Izmantotie dati: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv </a> <br> Ielādēts: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/javascript/global.js"></script>
<script type="module" src="/javascript/info.js"></script>
</html>