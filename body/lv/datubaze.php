<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/getingTrainRoute.php';
require_once __DIR__ . '/../../db/getingUsers.php';
require_once __DIR__ . '/../../db/initializeDB.php';

$trainDatabase = __DIR__ . '/../../storage/database/LatvianTrains.sqlite';
$userDatabase = __DIR__ . '/../../storage/database/Users.sqlite';

// automātiski lietotāju aizmet uz sakumlapa.php ja nav iegājis savā profilā un ja tam profilam nav administrātora tiesības
if (isset($_SESSION['tiesibas']) == false || $_SESSION['tiesibas'] != "administrators") {
    header("Location: sakumlapa.php");
    exit;
}

// izveido savienojumu ar datubāzēm
try {
    $trainConnection = getConnection($trainDatabase);
    $userConnection = getConnection($userDatabase);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {

    // iegūst visus datus no datubāzēm
    $calendar = getAllCalendar($trainConnection);
    $route = getAllRoutes($trainConnection);
    $stopTimes = getAllStopTimes($trainConnection);
    $stops = getAllStops($trainConnection);
    $trips = getAllTrips($trainConnection);
    $users = getAllUsers($userConnection);

} catch (Exception $e) {
    $e->getMessage();
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
    <link rel="stylesheet" href="/style/database.css">
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
        <ul id="tabuluPogas">
            <li>
                <button id="kalendars">Kalendārs</button>
            </li>
            <li>
                <button id="marsruts">Maršruti</button>
            </li>
            <li>
                <button id="stacija">Stacijas</button>
            </li>
            <li>
                <button id="apstasanas">Brauciena apstāšanās</button>
            </li>
            <li>
                <button id="braucieni">Braucieni</button>
            </li>
            <li>
                <button id="lietotaji">Lietotāji</button>
            </li>
        </ul>
    </div>
    <div class=tabulas>
        <table class="kalendaraTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Service id</label></th>
                    <th class="kolonnuNosaukumi" ><label>Monday</label></th>
                    <th class="kolonnuNosaukumi"><label>Tuesday</label></th>
                    <th class="kolonnuNosaukumi"><label>Wednesday</label></th>
                    <th class="kolonnuNosaukumi" ><label>Thursday</label></th>
                    <th class="kolonnuNosaukumi" ><label>Friday</label></th>
                    <th class="kolonnuNosaukumi"><label>Saturday</label></th>
                    <th class="kolonnuNosaukumi"><label>Sunday</label></th>
                    <th class="kolonnuNosaukumi"><label>Start date</label></th>
                    <th class="kolonnuNosaukumi"><label>End date</label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calendar as $c) : ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= $c['service_id'] ?></td>
                        <td><?= $c['monday'] ?></td>
                        <td><?= $c['tuesday'] ?></td>
                        <td><?= $c['wednesday'] ?></td>
                        <td><?= $c['thursday'] ?></td>
                        <td><?= $c['friday'] ?></td>
                        <td><?= $c['saturday'] ?></td>
                        <td><?= $c['sunday'] ?></td>
                        <td><?= $c['start_date'] ?></td>
                        <td><?= $c['end_date'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <table class="MarsrutaTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Route id</label></th>
                    <th class="kolonnuNosaukumi" ><label>Agency</label></th>
                    <th class="kolonnuNosaukumi"><label>Name</label></th>
                    <th class="kolonnuNosaukumi"><label>Type</label></th>
                    <th class="kolonnuNosaukumi" ><label>Color</label></th>
                    <th class="kolonnuNosaukumi" ><label>Text Color</label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($route as $r) : ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= $r['route_id'] ?></td>
                        <td><?= $r['agency'] ?></td>
                        <td><?= $r['name'] ?></td>
                        <td><?= $r['type'] ?></td>
                        <td><?= $r['color'] ?></td>
                        <td><?= $r['text_color'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <table class="StacijasTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Stop Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Name</label></th>
                    <th class="kolonnuNosaukumi"><label>Latitude</label></th>
                    <th class="kolonnuNosaukumi" ><label>Longitude</label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stops as $s) : ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= $s['stop_id'] ?></td>
                        <td><?= $s['name'] ?></td>
                        <td><?= $s['latitude'] ?></td>
                        <td><?= $s['longitude'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
       <table class="BraucienuApstasanasTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Trip Id</label></th>
                    <th class="kolonnuNosaukumi" ><label>Arrival Time</label></th>
                    <th class="kolonnuNosaukumi"><label>Departure Time</label></th>
                    <th class="kolonnuNosaukumi"><label>Stop Id</label></th>
                    <th class="kolonnuNosaukumi" ><label>Stop Sequence</label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stopTimes as $st) : ?>
                    <tr>
                        <td><?= $st['id'] ?></td>
                        <td><?= $st['trip_id'] ?></td>
                        <td><?= $st['arrival_time'] ?></td>
                        <td><?= $st['departure_time'] ?></td>
                        <td><?= $st['stop_id'] ?></td>
                        <td><?= $st['stop_sequence'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
       <table class="BraucienuTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Route Id</label></th>
                    <th class="kolonnuNosaukumi" ><label>Service Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Trip Id</label></th>
                    <th class="kolonnuNosaukumi" ><label>Headsign</label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $t) : ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><?= $t['route_id'] ?></td>
                        <td><?= $t['service_id'] ?></td>
                        <td><?= $t['trip_id'] ?></td>
                        <td><?= $t['headsign'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
       <table class="LietotajuTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Username</label></th>
                    <th class="kolonnuNosaukumi" ><label>Email</label></th>
                    <th class="kolonnuNosaukumi"><label>Rights</label></th>
                    <th class="kolonnuNosaukumi" ><label>Password</label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u) : ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= $u['username'] ?></td>
                        <td><?= $u['email'] ?></td>
                        <td><?= $u['rights'] ?></td>
                        <td><?= $u['password'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</body>
<footer class="mt-5 py-3">
    <p class="mb-0">© Latvijas vilcienu maršrutu kustības portāls <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Izmantotie dati: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv </a> <br> Ielādēts: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="/javascript/global.js"></script>
<script src="/javascript/database.js"></script>
</html>