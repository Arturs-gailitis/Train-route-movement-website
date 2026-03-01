<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/getingTrainRoute.php';
require_once __DIR__ . '/../../db/getingUsers.php';
require_once __DIR__ . '/../../db/initializeDB.php';
require_once __DIR__ . '/../../db/gettingNotifications.php';

$trainDatabase = __DIR__ . '/../../storage/database/LatvianTrains.sqlite';
$userDatabase = __DIR__ . '/../../storage/database/Users.sqlite';
$notificationDatabase = __DIR__ . '/../../storage/database/Notifications.sqlite';

$record = [];

// automātiski lietotāju aizmet uz sakumlapa.php ja nav iegājis savā profilā un ja tam profilam nav administrātora tiesības
if (isset($_SESSION['tiesibas']) == false || $_SESSION['tiesibas'] != "administrators") {
    header("Location: sakumlapa.php");
    exit;
}

// izveido savienojumu ar datubāzēm
try {
    $trainConnection = getConnection($trainDatabase);
    $userConnection = getConnection($userDatabase);
    $notificationsConnection = getConnection($notificationDatabase);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {

    if (isset($_GET['tabula']) != false && isset($_GET['id']) != false) {
        
        // iegūst ierakstu skatoties pēc url get metodes un id
        if ($_GET['tabula'] == "calendar") {
            $record = getCalendarByID($trainConnection, $_GET['id']);
        } else if ($_GET['tabula'] == "route") {
            $record = getRoutesByID($trainConnection, $_GET['id']);
        } else if ($_GET['tabula'] == "stops") {
            $record = getStopsByID($trainConnection, $_GET['id']);
        } else if ($_GET['tabula'] == "stop_time") {
            $record = getStopTimesByID($trainConnection, $_GET['id']);
        } else if ($_GET['tabula'] == "trips") {
            $record = getTripsByID($trainConnection, $_GET['id']);
        } else if ($_GET['tabula'] == "user") {
            $record = checkUserByParam($userConnection, $_GET['id'], "id");
        } else if ($_GET['tabula'] == "notification") {
            $record = getSpecificNotification($notificationsConnection, $_GET['id']);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_GET['tabula']) != false) {

            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Calendar 
            if ($_GET['tabula'] == "calendar") {

                $serviceId = $_POST['service_id'];
                $monday = trim($_POST['pirmdiena']);
                $tuesday = trim($_POST['otrdiena']);
                $wednesday = trim($_POST['tresdiena']);
                $thursday = trim($_POST['ceturtdiena']);
                $friday = trim($_POST['piekdiena']);
                $saturday = trim($_POST['sestdiena']);
                $sunday = trim($_POST['svetdiena']);
                $startDate = $_POST['sakumaDatums'];
                $endDate = $_POST['beiguDatums'];

                // rediģē ierakstu Calendar tabulā skatoties pēc id
                updateCalendar($trainConnection, $_GET['id'], $serviceId, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, 
                $sunday, $startDate, $endDate);

            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Route
            } else if ($_GET['tabula'] == "route") {

                $routeId = $_POST['route_id'];
                $agency = trim($_POST['agentura']);
                $name = trim($_POST['nosaukums']);
                $type = $_POST['tips'];
                $color = trim($_POST['krasa']);
                $textColor = trim($_POST['tKrasa']);

                // rediģē ierakstu Route tabulā skatoties pēc id
                updateRoutes($trainConnection, $_GET['id'], $routeId, $agency, $name, $type, $color, $textColor);

            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Stops
            } else if ($_GET['tabula'] == "stops") {

                $stopId = $_POST['stop_id'];
                $name = trim($_POST['nosaukums']);
                $lat = $_POST['lat'];
                $long = $_POST['long'];

                // rediģē ierakstu Stops tabulā skatoties pēc id
                updateStations($trainConnection, $_GET['id'], $stopId, $name, $lat, $long);
            
            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Stop_Times
            } else if ($_GET['tabula'] == "stop_time") {

                $tripId = $_POST['trip_id'];
                $arrival = $_POST['ierasanas'];
                $departure = $_POST['izbrauksana'];
                $stopId = $_POST['stop_id'];
                $sequence = $_POST['sekvence'];

                // rediģē ierakstu Stop_Times tabulā skatoties pēc id
                updateStopTimes($trainConnection, $_GET['id'], $tripId, $arrival, $departure, $stopId, $sequence);

            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Trips
            } else if ($_GET['tabula'] == "trips") {

                $routeId = $_POST['route_id'];
                $serviceId = $_POST['service_id'];
                $tripId = $_POST['trip_id'];
                $headsign = trim($_POST['apzimejums']);

                // rediģē ierakstu Trips tabulā skatoties pēc id
                updateTrips($trainConnection, $_GET['id'], $routeId, $serviceId, $tripId, $headsign);
            
            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Users
            } else if ($_GET['tabula'] == "user") {

                $rights = trim($_POST['tiesibas']);

                if ($rights == "lietotajs" || $rights == "administrators") {

                    // rediģē ierakstu Users tabulā skatoties pēc id un vai izvēlētās tiesībās atbilst lietotājam vai adminim 
                    changeRights($userConnection, $_GET['id'], $rights);
                }
            
            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Notifications
            } else if ($_GET['tabula'] == "notification") {

                $title = $_POST['virsraksts'];
                $image = $record['image'];
                $text = $_POST['teksts'];

                // rediģē ierakstu Notifications tabulā skatoties pēc id
                updateNotification($notificationsConnection, $_GET['id'], $title, $image, $text);
            }
        }
        
        header("Location: datubaze.php");
        exit();
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
    <link rel="stylesheet" href="/style/edit.css">
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
                    <a class="nav-link" href="pazinojumi.php">Paziņojumi</a>
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
                    <option value="http://localhost:8000/body/lv/rediget.php?tabula=<?php echo $_GET['tabula'] ?>&id=<?php echo $_GET['id'] ?>">Latviešu</option>
                    <option value="http://localhost:8000/body/eng/edit.php?tabula=<?php echo $_GET['tabula'] ?>&id=<?php echo $_GET['id'] ?>">Angļu</option>
                </select>
            </div>

        </div>
    </div>
    <div id="formasLaukums">
        <h2>Rediģēt ierakstu</h2>
        <form method="post" id=forma>
            <?php if ($_GET['tabula'] == "calendar"): ?>
                <div class="mb-3">
                    <label for="service_id">Service ID:</label>
                    <input type="text" name="service_id" value="<?php echo $record['service_id'] ?>">
                </div>
                <div class="mb-3">
                    <label for="pirmdiena">Pirmdiena:</label>
                    <input type="text" name="pirmdiena" value="<?php echo $record['monday'] ?>">
                </div>
                <div class="mb-3">
                    <label for="otrdiena">Otrdiena:</label>
                    <input type="text" name="otrdiena" value="<?php echo $record['tuesday'] ?>">
                </div>
                <div class="mb-3">
                    <label for="tresdiena">Trešdiena:</label>
                    <input type="text" name="tresdiena" value="<?php echo $record['wednesday'] ?>">
                </div>
                <div class="mb-3">
                    <label for="ceturtdiena">Ceturtdiena:</label>
                    <input type="text" name="ceturtdiena" value="<?php echo $record['thursday'] ?>">
                </div>
                <div class="mb-3">
                    <label for="piekdiena">Piektdiena:</label>
                    <input type="text" name="piekdiena" value="<?php echo $record['friday'] ?>">
                </div>
                <div class="mb-3">
                    <label for="sestdiena">Sestdiena:</label>
                    <input type="text" name="sestdiena" value="<?php echo $record['saturday'] ?>">
                </div>
                <div class="mb-3">
                    <label for="svetdiena">Svētdiena:</label>
                    <input type="text" name="svetdiena" value="<?php echo $record['sunday'] ?>">
                </div>
                <div class="mb-3">
                    <label for="sakumaDatums">Sākuma Datums:</label>
                    <input type="text" name="sakumaDatums" value="<?php echo $record['start_date'] ?>">
                </div>
                <div class="mb-3">
                    <label for="beiguDatums">Beigu Datums:</label>
                    <input type="text" name="beiguDatums" value="<?php echo $record['end_date'] ?>">
                </div>
            <?php elseif ($_GET['tabula'] == "route"): ?>
                <div class="mb-3">
                    <label for="route_id">Route ID:</label>
                    <input type="text" name="route_id" value="<?php echo $record['route_id'] ?>">
                </div>
                <div class="mb-3">
                    <label for="agentura">Aģentūra:</label>
                    <input type="text" name="agentura" value="<?php echo $record['agency'] ?>">
                </div>
                <div class="mb-3">
                    <label for="nosaukums">Nosaukums:</label>
                    <input type="text" name="nosaukums" value="<?php echo $record['name'] ?>">
                </div>
                <div class="mb-3">
                    <label for="tips">Tips:</label>
                    <input type="text" name="tips" value="<?php echo $record['type'] ?>">
                </div>
                <div class="mb-3">
                    <label for="krasa">Krāsa:</label>
                    <input type="text" name="krasa" value="<?php echo $record['color'] ?>">
                </div>
                <div class="mb-3">
                    <label for="tKrasa">Teksta Krāsa:</label>
                    <input type="text" name="tKrasa" value="<?php echo $record['text_color'] ?>">
                </div>
            <?php elseif ($_GET['tabula'] == "stops"): ?>
               <div class="mb-3">
                    <label for="stop_id">Stop ID:</label>
                    <input type="text" name="stop_id" value="<?php echo $record['stop_id'] ?>">
                </div>
                <div class="mb-3">
                    <label for="nosaukums">Nosaukums:</label>
                    <input type="text" name="nosaukums" value="<?php echo $record['name'] ?>">
                </div>
                <div class="mb-3">
                    <label for="lat">Platums:</label>
                    <input type="text" name="lat" value="<?php echo $record['latitude'] ?>">
                </div>
                <div class="mb-3">
                    <label for="long">Garums:</label>
                    <input type="text" name="long" value="<?php echo $record['longitude'] ?>">
                </div>
            <?php elseif ($_GET['tabula'] == "stop_time"): ?>
                <div class="mb-3">
                    <label for="trip_id">Trip ID:</label>
                    <input type="text" name="trip_id" value="<?php echo $record['trip_id'] ?>">
                </div>
                <div class="mb-3">
                    <label for="ierasanas">Ierašanās:</label>
                    <input type="text" name="ierasanas" value="<?php echo $record['arrival_time'] ?>">
                </div>
                <div class="mb-3">
                    <label for="izbrauksana">Izbraukšana:</label>
                    <input type="text" name="izbrauksana" value="<?php echo $record['departure_time'] ?>">
                </div>
                <div class="mb-3">
                    <label for="stop_id">Stop ID:</label>
                    <input type="text" name="stop_id" value="<?php echo $record['stop_id'] ?>">
                </div>
                <div class="mb-3">
                    <label for="sekvence">Sekvence:</label>
                    <input type="text" name="sekvence" value="<?php echo $record['stop_sequence'] ?>">
                </div>
            <?php elseif ($_GET['tabula'] == "trips"): ?> 
               <div class="mb-3">
                    <label for="route_id">Route ID:</label>
                    <input type="text" name="route_id" value="<?php echo $record['route_id'] ?>">
                </div>
                <div class="mb-3">
                    <label for="service_id">Service ID:</label>
                    <input type="text" name="service_id" value="<?php echo $record['service_id'] ?>">
                </div>
                <div class="mb-3">
                    <label for="trip_id">Trip ID:</label>
                    <input type="text" name="trip_id" value="<?php echo $record['trip_id'] ?>">
                </div>
                <div class="mb-3">
                    <label for="apzimejums">Apzīmējums:</label>
                    <input type="text" name="apzimejums" value="<?php echo $record['headsign'] ?>">
                </div>
            <?php elseif ($_GET['tabula'] == "user"): ?>
               <div class="mb-3">
                    <label>
                        Lietotājvārds: <span><?php echo $record['username'] ?></span>
                    </label>
                </div>
                <div class="mb-3">
                    <label>
                        Epasts: <span><?php echo $record['email'] ?></span>
                    </label>
                </div>
                <div class="mb-3">
                    <label for="tiesibas">Tiesības:</label>
                    <input type="text" name="tiesibas" value="<?php echo $record['rights'] ?>">
                </div>
                <div class="mb-3">
                    <label>
                        Parole: <span><?php echo $record['password'] ?></span>
                    </label>
                </div>
            <?php elseif ($_GET['tabula'] == "notification"): ?>
                <div class="mb-3">
                    <label for="virsraksts">Virsraksts:</label>
                    <textarea name="virsraksts"><?php echo $record['title'] ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="bilde">Faila atrašanās vieta:</label>
                    <label name="bilde"><?php echo $record['image'] ?></label>
                </div>
                <div class="mb-3">
                    <label for="teksts">Teksts:</label>
                    <textarea name="teksts"><?php echo $record['info'] ?></textarea>
                </div>
            <?php endif ?>
            <a class="btn btn-primary" href="datubaze.php">Atcelt</a>
            <button class="btn btn-primary" type="submit">Atjaunot</button>
        </form>
    </div>
</body>
<footer class="mt-5 py-3">
    <p class="mb-0">© Latvijas vilcienu maršrutu kustības portāls <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Izmantotie dati: <a href="https://www.vivi.lv/lv/sadarbiba/atvertie-dati/">
            vivi.lv </a> <br> Ielādēts: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="/javascript/global.js"></script>
</html>