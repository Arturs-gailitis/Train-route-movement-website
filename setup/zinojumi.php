<?php 

// saglabā sesiju
session_start();

require_once __DIR__.'/../db/initializeDB.php';
$database = __DIR__ . '/../storage/database/Notifications.sqlite';

// izveido savienojumu ar datubāzi
$conection = getConnection($database);

// izdzēš veco Notifications tabulu
$deleteTable = "DROP TABLE IF EXISTS Notifications";
$conection->exec($deleteTable);

// izveido jaunu Notifications tabulu
$createNotificationQuerry = "CREATE TABLE IF NOT EXISTS Notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    image TEXT NOT NULL,
    info TEXT NOT NULL
);";
$conection->exec($createNotificationQuerry);
?>

<!DOCTYPE html>
<html lang="lv">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvijas vilcienu maršrutu kustības portāls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/global.css">
    <link rel="stylesheet" href="/style/setup.css">
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
                        <a class="nav-link" href="/body/lv/datubaze.php">Datubāze</a>
                    </li>
                <?php endif ?>
                <li class="nav-item">
                    <a class="nav-link" href="/body/lv/sakumlapa.php">Sākumlapa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/body/lv/pazinojumi.php">Paziņojumi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/body/lv/kontakti.php">Kontakti</a>
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
                        <a class = "profilaStatuss" href="/body/lv/pieteikties.php">Pieslēdzies savā kontā</a>
                    </li>
                    <li>
                        <a class = "profilaStatuss" href="/body/lv/registracija.php">Izveido jaunu kontu</a>
                    </li>
                <?php elseif (isset($_SESSION['lietotajvards'])): ?>
                    <li>
                        <a class = "profilaStatuss" id="iziesana" href="/body/lv/iziet.php">Iziet ārā no sava konta</a>
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
                    <option value="http://localhost:8000/setup/zinojumi.php">Latviešu</option>
                    <option value="http://localhost:8000/setup/notifications.php">Angļu</option>
                </select>
            </div>

        </div>
    </div>
    <div id="laukums">
        <h2>Tabula ir izveidotas veiksmīgi</h2>
        <a class="btn btn-primary" href="/body/lv/datubaze.php">Doties atpakaļ</a>
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