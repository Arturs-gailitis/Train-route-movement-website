<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/initializeDB.php';
require_once __DIR__ . '/../../db/gettingNotifications.php';

$notificationDatabase = __DIR__ . '/../../storage/database/Notifications.sqlite';
$record = [];

// izveido savienojumu ar datubāzi
try {
    $notificationsConnection = getConnection($notificationDatabase);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    // ja paziņojumus filtrēs ar atslēgvārdu
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atslegvards']) == true) {

        // iegūst paziņojumus, kas asociējās ar atslēgvārdu
        $keyword = $_POST['atslegvards'];
        $record = searchByKeyword($notificationsConnection, $keyword);

    } else {
        // iegūst visus paziņojumus
        $record = getAllNotifications($notificationsConnection);
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
    <link rel="stylesheet" href="/style/notifications.css">
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
                    <option value="http://localhost:8000/body/eng/notifications.php">English</option>
                    <option value="http://localhost:8000/body/lv/pazinojumi.php">Latvian</option>
                </select>
            </div>

        </div>
    </div>
    <div class="container mt-4">
        <h2 class="mt-4">Current notifications</h2>
        <form method="POST" class="form-inline mb-4">
            <input type="text" name="atslegvards" class="form-control mr-2" placeholder="Search notifications by keyword">
            <button id="meklet" type="submit" class="btn btn-primary">Search</button>
        </form>
        <?php if (empty($record)): ?>
            <img src="/icons/error.svg" alt="Error sign" class="kluda">
            <h3 id="navPazinojumi">No current notifications have been published.</h3>
        <?php else: ?>
            <div class="row">
                <?php foreach ($record as $r): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card">
                            <img src="/../../<?php echo $r['image'] ?>" class="card-img-top service-img" alt="There is no image available for the notification.">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo $r['title'] ?></h3>
                                <hr>
                                <p class="card-text"><?php echo $r['info'] ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
<script src="/javascript/notifications.js"></script>
</html>