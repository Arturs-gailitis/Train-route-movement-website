<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/getingTrainRoute.php';
require_once __DIR__ . '/../../db/gettingNotifications.php';
require_once __DIR__ . '/../../db/initializeDB.php';

$trainDatabase = __DIR__ . '/../../storage/database/LatvianTrains.sqlite';
$notificationDatabase = __DIR__ . '/../../storage/database/Notifications.sqlite';

$error = null;

// automātiski lietotāju aizmet uz sakumlapa.php ja nav iegājis savā profilā un ja tam profilam nav administrātora tiesības
if (isset($_SESSION['tiesibas']) == false || $_SESSION['tiesibas'] != "administrators") {
    header("Location: sakumlapa.php");
    exit;
}

// izveido savienojumu ar datubāzēm
try {
    $trainConnection = getConnection($trainDatabase);
    $notificationConnection = getConnection($notificationDatabase);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {

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

                // Skatās vai tāds service_id nav izmantots, ja ir tad parāda kļūdu, ja nav tad izveido Calendar ierakstu 
                if (usedId($trainConnection, "Calendar", $serviceId, "service_id") == false) {
                    $error = "Ievadītais service_id jau tiek izmantots.";
                } else {
                    createCalendar($trainConnection, $serviceId, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday,
                        $sunday, $startDate, $endDate);
                }
            
            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Route
            } else if ($_GET['tabula'] == "routes") {

                $routeId = $_POST['route_id'];
                $agency = trim($_POST['agentura']);
                $name = trim($_POST['nosaukums']);
                $type = $_POST['tips'];
                $color = trim($_POST['krasa']);
                $textColor = trim($_POST['tKrasa']);

                // Skatās vai tāds route_id nav izmantots, ja ir tad parāda kļūdu, ja nav tad izveido Route ierakstu
                if (usedId($trainConnection, "Routes", $routeId, "route_id") == false) {
                    $error = "Ievadītais route_id jau tiek izmantots.";
                } else {
                    createRoute($trainConnection, $routeId, $agency, $name, $type, $color, $textColor);
                }
            
            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Stops
            } else if ($_GET['tabula'] == "stops") {
                
                $stopId = $_POST['stop_id'];
                $name = trim($_POST['nosaukums']);
                $lat = $_POST['lat'];
                $long = $_POST['long'];

                // Skatās vai tāds stop_id nav izmantots, ja ir tad parāda kļūdu, ja nav tad izveido Stops ierakstu
                if (usedId($trainConnection, "Stops", $stopId, "stop_id") == false) {
                    $error = "Ievadītais stop_id jau tiek izmantots.";
                } else {
                    createStops($trainConnection, $stopId, $name, $lat, $long);
                }
            
            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Stop_Times
            } else if ($_GET['tabula'] == "stop_times") {
                
                $tripId = $_POST['trip_id'];
                $arrival = $_POST['ierasanas'];
                $departure = $_POST['izbrauksana'];
                $stopId = $_POST['stop_id'];
                $sequence = $_POST['sekvence'];

                // Izveido Stop_Times ierakstu
                createStopTimes($trainConnection, $tripId, $arrival, $departure, $stopId, $sequence);
            
            // skatās pēc get metodes izveidotā url vai izvēlētā tabula ir Trips
            } else if ($_GET['tabula'] == "trips") {

                $routeId = $_POST['route_id'];
                $serviceId = $_POST['service_id'];
                $tripId = $_POST['trip_id'];
                $headsign = trim($_POST['apzimejums']);

                // Skatās vai tāds stop_id nav izmantots, ja ir tad parāda kļūdu, ja nav tad izveido Trips ierakstu
                if (usedId($trainConnection, "Trips", $tripId, "trip_id") == false) {
                    $error = "Ievadītais trip_id jau tiek izmantots.";
                } else {
                    createTrip($trainConnection, $routeId, $serviceId, $tripId, $headsign);
                }

            } else if ($_GET['tabula'] == "notifications") {

                $title = trim($_POST['virsraksts']);
                $text = trim($_POST['teksts']);
                $image = "";

                $notificationError = null;

                // pārbauda vai ievietotā bilde tika veiksmīgi ielikta formā
                if (isset($_FILES['bilde']) && $_FILES['bilde']['error'] == UPLOAD_ERR_OK) {
                    $map = 'icons/notifications';

                    // failu formātu saraksts, kuri drīkst ielikt
                    $allowedFileType = ["image/jpeg", "image/png", "image/svg+xml"];
                    $allowed = false;
                    
                    // pārbauda vai ieliktās bildes failu formāts atbilst
                    foreach ($allowedFileType as $a) {
                        if ($a == $_FILES['bilde']['type']) {
                            $allowed = true;
                        }
                    }

                    if ($allowed == true) {
                        // veido jaunās bildes nosaukumu, kamēr tas neatkārtojās
                        do {
                            $picture = rand(1, 9999) . "-" . date("Y.m.j") . "-" . preg_replace('/\s+/u', '_', basename($_FILES['bilde']['name']));
                        } while (file_exists(__DIR__ . "/../../" . $map . "/" . $picture) == true);

                        // izveido visu bildes lokāciju un to pārvieto uz icons/notifications
                        $image = $map . "/" . $picture;
                        move_uploaded_file($_FILES['bilde']['tmp_name'], __DIR__ . '/../../' . $image);

                    } else {
                        $notificationError = "Nav derīgs failu tips.";
                    }
                
                } else {
                    $notificationError = "Attēla augšupielāde nav bijusi veiksmīga.";
                }

                // ja nav kļūdu, tad ieliek paziņojumu datubāzē
                if ($notificationError == null) {
                    insertNotification($notificationConnection, $title, $image, $text);
                } else {
                    $error = $notificationError;
                }
            }

            // Ja nav kļūda, tad lietotāju sūta uz datubaze.php
            if ($error == null) {
                header("Location: datubaze.php");
                exit();
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
    <link rel="stylesheet" href="/style/global.css">
    <link rel="stylesheet" href="/style/create.css">
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
                    <option value="http://localhost:8000/body/lv/izveidot.php?tabula=<?php echo $_GET['tabula'] ?>">Latviešu</option>
                    <option value="http://localhost:8000/body/eng/create.php?tabula=<?php echo $_GET['tabula'] ?>">Angļu</option>
                </select>
            </div>

        </div>
    </div>
    <div id="formasLaukums">
        <h2>Izveidot ierakstu</h2>
        <?php if ($error != null): ?>
            <span id="kluda"><?php echo $error ?></span>
        <?php endif ?>
        <form method="post" id=forma enctype="multipart/form-data">
            <?php if ($_GET['tabula'] == "calendar"): ?>
                <div class="mb-3">
                    <label for="service_id">Service ID:</label>
                    <input type="text" name="service_id" >
                </div>
                <div class="mb-3">
                    <label for="pirmdiena">Pirmdiena:</label>
                    <input type="text" name="pirmdiena">
                </div>
                <div class="mb-3">
                    <label for="otrdiena">Otrdiena:</label>
                    <input type="text" name="otrdiena">
                </div>
                <div class="mb-3">
                    <label for="tresdiena">Trešdiena:</label>
                    <input type="text" name="tresdiena">
                </div>
                <div class="mb-3">
                    <label for="ceturtdiena">Ceturtdiena:</label>
                    <input type="text" name="ceturtdiena">
                </div>
                <div class="mb-3">
                    <label for="piekdiena">Piektdiena:</label>
                    <input type="text" name="piekdiena">
                </div>
                <div class="mb-3">
                    <label for="sestdiena">Sestdiena:</label>
                    <input type="text" name="sestdiena">
                </div>
                <div class="mb-3">
                    <label for="svetdiena">Svētdiena:</label>
                    <input type="text" name="svetdiena">
                </div>
                <div class="mb-3">
                    <label for="sakumaDatums">Sākuma Datums:</label>
                    <input type="text" name="sakumaDatums">
                </div>
                <div class="mb-3">
                    <label for="beiguDatums">Beigu Datums:</label>
                    <input type="text" name="beiguDatums">
                </div>
            <?php elseif ($_GET['tabula'] == "routes"): ?>
                <div class="mb-3">
                    <label for="route_id">Route ID:</label>
                    <input type="text" name="route_id">
                </div>
                <div class="mb-3">
                    <label for="agentura">Aģentūra:</label>
                    <input type="text" name="agentura">
                </div>
                <div class="mb-3">
                    <label for="nosaukums">Nosaukums:</label>
                    <input type="text" name="nosaukums">
                </div>
                <div class="mb-3">
                    <label for="tips">Tips:</label>
                    <input type="text" name="tips">
                </div>
                <div class="mb-3">
                    <label for="krasa">Krāsa:</label>
                    <input type="text" name="krasa">
                </div>
                <div class="mb-3">
                    <label for="tKrasa">Teksta Krāsa:</label>
                    <input type="text" name="tKrasa">
                </div>
            <?php elseif ($_GET['tabula'] == "stops"): ?>
               <div class="mb-3">
                    <label for="stop_id">Stop ID:</label>
                    <input type="text" name="stop_id">
                </div>
                <div class="mb-3">
                    <label for="nosaukums">Nosaukums:</label>
                    <input type="text" name="nosaukums">
                </div>
                <div class="mb-3">
                    <label for="lat">Platums:</label>
                    <input type="text" name="lat">
                </div>
                <div class="mb-3">
                    <label for="long">Garums:</label>
                    <input type="text" name="long">
                </div>
            <?php elseif ($_GET['tabula'] == "stop_times"): ?>
                <div class="mb-3">
                    <label for="trip_id">Trip ID:</label>
                    <input type="text" name="trip_id">
                </div>
                <div class="mb-3">
                    <label for="ierasanas">Ierašanās:</label>
                    <input type="text" name="ierasanas">
                </div>
                <div class="mb-3">
                    <label for="izbrauksana">Izbraukšana:</label>
                    <input type="text" name="izbrauksana">
                </div>
                <div class="mb-3">
                    <label for="stop_id">Stop ID:</label>
                    <input type="text" name="stop_id">
                </div>
                <div class="mb-3">
                    <label for="sekvence">Sekvence:</label>
                    <input type="text" name="sekvence">
                </div>
            <?php elseif ($_GET['tabula'] == "trips"): ?> 
               <div class="mb-3">
                    <label for="route_id">Route ID:</label>
                    <input type="text" name="route_id">
                </div>
                <div class="mb-3">
                    <label for="service_id">Service ID:</label>
                    <input type="text" name="service_id">
                </div>
                <div class="mb-3">
                    <label for="trip_id">Trip ID:</label>
                    <input type="text" name="trip_id">
                </div>
                <div class="mb-3">
                    <label for="apzimejums">Apzīmējums:</label>
                    <input type="text" name="apzimejums">
                </div>
            <?php elseif ($_GET['tabula'] == "notifications"): ?> 
                <div class="mb-3">
                    <label for="virsraksts">Virsraksts:</label>
                    <textarea name="virsraksts"></textarea>
                </div>
                <div class="mb-3">
                    <label for="bilde">Bildes atrašanās vieta:</label>
                    <input type="file" name="bilde">
                </div>
                <div class="mb-3">
                    <label for="teksts">Teksts:</label>
                    <textarea name="teksts"></textarea>
                </div>
            <?php endif ?>
            <a class="btn btn-primary" href="datubaze.php">Atcelt</a>
            <button class="btn btn-primary" type="submit">Izveidot ierakstu</button>
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