<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/getingTrainRoute.php';
require_once __DIR__ . '/../../db/gettingNotifications.php';
require_once __DIR__ . '/../../db/initializeDB.php';

$trainDatabase = __DIR__ . '/../../storage/database/LatvianTrains.sqlite';
$notificationDatabase = __DIR__ . '/../../storage/database/Notifications.sqlite';

$error = null;

// automātiski lietotāju aizmet uz sakumlapu ja nav iegājis savā profilā un ja tam profilam nav administrātora tiesības
if (isset($_SESSION['tiesibas']) == false || $_SESSION['tiesibas'] != "administrators") {
    header("Location: main.php");
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
                    $error = "The service_id you entered is already in use.";
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
                    $error = "The route_id entered is already in use.";
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
                    $error = "The stop_id entered is already in use.";
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
                    $error = "The trip_id you entered is already in use.";
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
                        $notificationError = "Invalid file type.";
                    }
                
                } else {
                    $notificationError = "Image upload was not successful.";
                }

                // ja nav kļūdu, tad ieliek paziņojumu datubāzē
                if ($notificationError == null) {
                    insertNotification($notificationConnection, $title, $image, $text);
                } else {
                    $error = $notificationError;
                }
            }

            // Ja nav kļūda, tad lietotāju sūta uz datubase.php
            if ($error == null) {
                header("Location: database.php");
                exit();
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
    <link rel="stylesheet" href="/style/create.css">
    <link rel="stylesheet" href="/style/global.css">
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
                    <option value="http://localhost:8000/body/eng/create.php?tabula=<?php echo $_GET['tabula'] ?>">English</option>
                    <option value="http://localhost:8000/body/lv/izveidot.php?tabula=<?php echo $_GET['tabula'] ?>">Latvian</option>
                </select>
            </div>

        </div>
    </div>
    <div id="formasLaukums">
        <h2>Create a record</h2>
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
                    <label for="pirmdiena">Monday:</label>
                    <input type="text" name="pirmdiena">
                </div>
                <div class="mb-3">
                    <label for="otrdiena">Tuesday:</label>
                    <input type="text" name="otrdiena">
                </div>
                <div class="mb-3">
                    <label for="tresdiena">Wednesday:</label>
                    <input type="text" name="tresdiena">
                </div>
                <div class="mb-3">
                    <label for="ceturtdiena">Thursday:</label>
                    <input type="text" name="ceturtdiena">
                </div>
                <div class="mb-3">
                    <label for="piekdiena">Friday:</label>
                    <input type="text" name="piekdiena">
                </div>
                <div class="mb-3">
                    <label for="sestdiena">Saturday:</label>
                    <input type="text" name="sestdiena">
                </div>
                <div class="mb-3">
                    <label for="svetdiena">Sunday:</label>
                    <input type="text" name="svetdiena">
                </div>
                <div class="mb-3">
                    <label for="sakumaDatums">Start Date:</label>
                    <input type="text" name="sakumaDatums">
                </div>
                <div class="mb-3">
                    <label for="beiguDatums">End Date:</label>
                    <input type="text" name="beiguDatums">
                </div>
            <?php elseif ($_GET['tabula'] == "routes"): ?>
                <div class="mb-3">
                    <label for="route_id">Route ID:</label>
                    <input type="text" name="route_id">
                </div>
                <div class="mb-3">
                    <label for="agentura">Agency:</label>
                    <input type="text" name="agentura">
                </div>
                <div class="mb-3">
                    <label for="nosaukums">Name:</label>
                    <input type="text" name="nosaukums">
                </div>
                <div class="mb-3">
                    <label for="tips">Type:</label>
                    <input type="text" name="tips">
                </div>
                <div class="mb-3">
                    <label for="krasa">Color:</label>
                    <input type="text" name="krasa">
                </div>
                <div class="mb-3">
                    <label for="tKrasa">Text color:</label>
                    <input type="text" name="tKrasa">
                </div>
            <?php elseif ($_GET['tabula'] == "stops"): ?>
               <div class="mb-3">
                    <label for="stop_id">Stop ID:</label>
                    <input type="text" name="stop_id">
                </div>
                <div class="mb-3">
                    <label for="nosaukums">Name:</label>
                    <input type="text" name="nosaukums">
                </div>
                <div class="mb-3">
                    <label for="lat">Latitude:</label>
                    <input type="text" name="lat">
                </div>
                <div class="mb-3">
                    <label for="long">Longitude:</label>
                    <input type="text" name="long">
                </div>
            <?php elseif ($_GET['tabula'] == "stop_times"): ?>
                <div class="mb-3">
                    <label for="trip_id">Trip ID:</label>
                    <input type="text" name="trip_id">
                </div>
                <div class="mb-3">
                    <label for="ierasanas">Arrival:</label>
                    <input type="text" name="ierasanas">
                </div>
                <div class="mb-3">
                    <label for="izbrauksana">Departure:</label>
                    <input type="text" name="izbrauksana">
                </div>
                <div class="mb-3">
                    <label for="stop_id">Stop ID:</label>
                    <input type="text" name="stop_id">
                </div>
                <div class="mb-3">
                    <label for="sekvence">Sequence:</label>
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
                    <label for="apzimejums">Designation:</label>
                    <input type="text" name="apzimejums">
                </div>
            <?php elseif ($_GET['tabula'] == "notifications"): ?> 
                <div class="mb-3">
                    <label for="virsraksts">Title:</label>
                    <textarea name="virsraksts"></textarea>
                </div>
                <div class="mb-3">
                    <label for="bilde">Image location:</label>
                    <input type="file" name="bilde">
                </div>
                <div class="mb-3">
                    <label for="teksts">Text:</label>
                    <textarea name="teksts"></textarea>
                </div>
            <?php endif ?>
            <a class="btn btn-primary" href="database.php">Cancel</a>
            <button class="btn btn-primary" type="submit">Create a record</button>
        </form>
    </div>
</body>
<footer class="mt-5 py-3">
    <p class="mb-0">© Latvian Train Route Portal <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Data used: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv </a> <br> Loaded: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="/javascript/global.js"></script>
</html>