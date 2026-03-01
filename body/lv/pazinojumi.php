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
<html lang="lv">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvijas vilcienu maršrutu kustības portāls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/global.css">
    <link rel="stylesheet" href="/style/notifications.css">
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
                    <option value="http://localhost:8000/body/lv/pazinojumi.php">Latviešu</option>
                    <option value="http://localhost:8000/body/eng/notifications.php">Angļu</option>
                </select>
            </div>

        </div>
    </div>
    <div class="container mt-4">
        <h2 class="mt-4">Aktuālie paziņojumi</h2>
        <form method="POST" class="form-inline mb-4">
            <input type="text" name="atslegvards" class="form-control mr-2" placeholder="Meklēt paziņojumus pēc atslēgvārda">
            <button id="meklet" type="submit" class="btn btn-primary">Meklēt</button>
        </form>
        <?php if (empty($record)): ?>
            <img src="/icons/info.svg" alt="Klūdas zīme" class="kluda">
            <h3 id="navPazinojumi">Nav publicēti nekādi aktuālie paziņojumi.</h3>
        <?php else: ?>
            <div class="row">
                <?php foreach ($record as $r): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card">
                            <img src="/../../<?php echo $r['image'] ?>" class="card-img-top service-img" alt="Pieejamam paziņojumam nav bildes.">
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
    <p class="mb-0">© Latvijas vilcienu maršrutu kustības portāls <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Izmantotie dati: <a href="https://www.vivi.lv/lv/sadarbiba/atvertie-dati/">
            vivi.lv </a> <br> Ielādēts: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="/javascript/global.js"></script>
<script src="/javascript/notifications.js"></script>
</html>