<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/getingTrainRoute.php';
require_once __DIR__ . '/../../db/getingUsers.php';
require_once __DIR__ . '/../../db/gettingMesages.php';
require_once __DIR__ . '/../../db/initializeDB.php';
require_once __DIR__ . '/../../db/gettingNotifications.php';

$trainDatabase = __DIR__ . '/../../storage/database/LatvianTrains.sqlite';
$userDatabase = __DIR__ . '/../../storage/database/Users.sqlite';
$messageDatabase = __DIR__ . '/../../storage/database/UserMessages.sqlite';
$notificationDatabase = __DIR__ . '/../../storage/database/Notifications.sqlite';

$calendar = [];
$route = [];
$stopTimes = [];
$stops = [];
$trips = [];
$users = [];
$messages = [];
$notifications = [];

// automātiski lietotāju aizmet uz sakumlapu ja nav iegājis savā profilā un ja tam profilam nav administrātora tiesības
if (isset($_SESSION['tiesibas']) == false || $_SESSION['tiesibas'] != "administrators") {
    header("Location: main.php");
    exit;
}

// izveido savienojumu ar datubāzēm
try {
    $trainConnection = getConnection($trainDatabase);
    $userConnection = getConnection($userDatabase);
    $messagesConnection = getConnection($messageDatabase);
    $notificationsConnection = getConnection($notificationDatabase);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        if (isset($_GET['tabula']) && isset($_GET['id'])) {

            // Izdzēš ierakstu datubāzēs skatoties pēc url get metodes un id
            if ($_GET['tabula'] == "calendar") {
                deleteCalendar($trainConnection, $_GET['id']);
            } else if ($_GET['tabula'] == "route") {
                deleteRoute($trainConnection, $_GET['id']);
            } else if ($_GET['tabula'] == "stops") {
                deleteStop($trainConnection, $_GET['id']);
            } else if ($_GET['tabula'] == "stop_time") {
                deleteStopTime($trainConnection, $_GET['id']);
            } else if ($_GET['tabula'] == "trips") {
                deleteTrip($trainConnection, $_GET['id']);
            } else if ($_GET['tabula'] == "user") {
                deleteUser($userConnection, $_GET['id']);
            } else if ($_GET['tabula'] == "message") {
                deleteMessage($messagesConnection, $_GET['id']);
            } else if ($_GET['tabula'] == "notification") {
                $record = getSpecificNotification($notificationsConnection, $_GET['id']);

                // izdzēš veco bildi no icons/notifications
                if (file_exists(__DIR__ . "/../../" . $record['image'])) {
                    unlink(__DIR__ . "/../../" . $record['image']);
                }

                deleteNotification($notificationsConnection, $_GET['id']);
            }

            header("Location: datubase.php");
            exit();

        }
    }

    // iegūst visus datus no datubāzēm
    $calendar = getAllCalendar($trainConnection);
    $route = getAllRoutes($trainConnection);
    $stopTimes = getAllStopTimes($trainConnection);
    $stops = getAllStops($trainConnection);
    $trips = getAllTrips($trainConnection);
    $users = getAllUsers($userConnection);
    $messages = getAllMessages($messagesConnection);
    $notifications = getAllNotifications($notificationsConnection);

} catch (Exception $e) {
    $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="eng">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvian Train Route Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/database.css">
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
                    <option value="http://localhost:8000/body/eng/database.php">English</option>
                    <option value="http://localhost:8000/body/lv/datubaze.php">Latvian</option>
                </select>
            </div>

        </div>
    </div>
    <div id="paraditPoguSadalu">
        <button type="button" id="atvertPoguSadaļasPoga" title="Show button section">
            <img src="/icons/arrow-down.svg" alt="Show button section" id="atvērtPoguSadaļasIkona">
        </button>
    </div>
    </div>
    <div id="poguLaukums">
        <div>
            <h3 id="pirmais">Restart database data</h3>
            <ul id="restartet">
                <li>
                    <button class="btn btn-primary" id="vilcieni">Restart train data</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="lietotaja">Restartē lietotāja datus</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="kontakti">Restarts user data</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="pazinojumi">Restarts message data</button>
                </li>
            </ul>
            <hr>
        </div>
        <div>
            <h3>Switch to other tables</h3>
            <ul id="tabuluPogas">
                <li>
                    <button class="btn btn-primary" id="kalendars">Calendar</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="marsruts">Routes</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="stacija">Stations</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="apstasanas">Trip stop</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="braucieni">Trips</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="lietotaji">Users</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="zinojumi">Messages</button>
                </li>
                <li>
                    <button class="btn btn-primary" id="paz">Notifications</button>
                </li>
            </ul>
            <hr>
        </div>
        <div>
            <h3>Create a new entry</h3>
            <a class="btn btn-primary" id="izveidot">Create</a>
        </div>
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
                    <th class="kolonnuNosaukumi" ><label>Ceturtdiena</label></th>
                    <th class="kolonnuNosaukumi" ><label>Thursday</label></th>
                    <th class="kolonnuNosaukumi"><label>Saturday</label></th>
                    <th class="kolonnuNosaukumi"><label>Sunday</label></th>
                    <th class="kolonnuNosaukumi"><label>Start date</label></th>
                    <th class="kolonnuNosaukumi"><label>End date</label></th>
                    <th class="kolonnuNosaukumi"><label>Activities</label></th>
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
                        <td class= darbibas>
                            <a class="btn btn-primary btn-sm rediget" href="rediget.php?tabula=calendar&id=<?php echo $c['id'] ?>">
                                Edit
                            </a>
                            <a class="btn btn-primary btn-sm dzest" href="datubaze.php?tabula=calendar&id=<?php echo $c['id'] ?>">
                                Delete
                            </a>
                        </td>
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
                    <th class="kolonnuNosaukumi"><label>Route Name</label></th>
                    <th class="kolonnuNosaukumi"><label>Type</label></th>
                    <th class="kolonnuNosaukumi" ><label>Color</label></th>
                    <th class="kolonnuNosaukumi" ><label>Text color</label></th>
                    <th class="kolonnuNosaukumi"><label>Actions</label></th>
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
                        <td class= darbibas>
                            <a class="btn btn-primary btn-sm rediget" href="rediget.php?tabula=route&id=<?php echo $r['id'] ?>">
                                Edit
                            </a>
                            <a class="btn btn-primary btn-sm dzest" href="datubaze.php?tabula=route&id=<?php echo $r['id'] ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <table class="StacijasTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Stop Id</label></th>
                    <th class="kolonnuNosaukumi"><label>Station Name</label></th>
                    <th class="kolonnuNosaukumi"><label>Latitude</label></th>
                    <th class="kolonnuNosaukumi" ><label>Longitude</label></th>
                    <th class="kolonnuNosaukumi"><label>Actions</label></th>
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
                        <td class= darbibas>
                            <a class="btn btn-primary btn-sm rediget" href="rediget.php?tabula=stops&id=<?php echo $s['id'] ?>">
                                Edit
                            </a>
                            <a class="btn btn-primary btn-sm dzest" href="datubaze.php?tabula=stops&id=<?php echo $s['id'] ?>">
                                Delete
                            </a>
                        </td>
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
                    <th class="kolonnuNosaukumi" ><label>Stopping Sequence</label></th>
                    <th class="kolonnuNosaukumi"><label>Actions</label></th>
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
                        <td class= darbibas>
                            <a class="btn btn-primary btn-sm rediget" href="rediget.php?tabula=stop_time&id=<?php echo $st['id'] ?>">
                                Edit
                            </a>
                            <a class="btn btn-primary btn-sm dzest" href="datubaze.php?tabula=stop_time&id=<?php echo $st['id'] ?>">
                                Delete
                            </a>
                        </td>
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
                    <th class="kolonnuNosaukumi" ><label>Route Destination Designation</label></th>
                    <th class="kolonnuNosaukumi"><label>Actions</label></th>
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
                        <td class= darbibas>
                            <a class="btn btn-primary btn-sm rediget" href="rediget.php?tabula=trips&id=<?php echo $t['id'] ?>">
                                Edit
                            </a>
                            <a class="btn btn-primary btn-sm dzest" href="datubaze.php?tabula=trips&id=<?php echo $t['id'] ?>">
                                Delete
                            </a>
                        </td>
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
                    <th class="kolonnuNosaukumi"><label>Actions</label></th>
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
                        <td class= darbibas>
                            <a class="btn btn-primary btn-sm rediget" href="rediget.php?tabula=user&id=<?php echo $u['id'] ?>">
                                Edit
                            </a>
                            <a class="btn btn-primary btn-sm dzest" href="datubaze.php?tabula=user&id=<?php echo $u['id'] ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <table class= "ZinuTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>ID</label></th>
                    <th class="kolonnuNosaukumi"><label>Email</label></th>
                    <th class="kolonnuNosaukumi"><label>Message</label></th>
                    <th class="kolonnuNosaukumi"><label>Actions</label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $m) :  ?>
                    <tr>
                        <td><?= $m['id'] ?></td>
                        <td><?= $m['email'] ?></td>
                        <td><?= $m['message'] ?></td>
                        <td id="darbibas">
                            <a class="btn btn-primary btn-sm dzest" href="datubaze.php?tabula=message&id=<?php echo $m['id'] ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <table class= "PazinojumuTabula">
            <thead>
                <tr>
                    <th class="kolonnuNosaukumi"><label>ID</label></th>
                    <th class="kolonnuNosaukumi"><label>Title</label></th>
                    <th class="kolonnuNosaukumi"><label>Image location</label></th>
                    <th class="kolonnuNosaukumi"><label>Text</label></th>
                    <th class="kolonnuNosaukumi"><label>Actions</label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $n) :  ?>
                    <tr>
                        <td><?= $n['id'] ?></td>
                        <td><?= $n['title'] ?></td>
                        <td><?= $n['image'] ?></td>
                        <td><?= $n['info'] ?></td>
                        <td id="darbibas">
                            <a class="btn btn-primary btn-sm rediget" href="rediget.php?tabula=notification&id=<?php echo $n['id'] ?>">
                                Edit
                            </a>
                            <a class="btn btn-primary btn-sm dzest" href="datubaze.php?tabula=notification&id=<?php echo $n['id'] ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
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
<script src="/javascript/database.js"></script>
</html>