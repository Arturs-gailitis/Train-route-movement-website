<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/initializeDB.php';
require_once __DIR__ . '/../../db/gettingMesages.php';
$database = __DIR__ . '/../../storage/database/UserMessages.sqlite';
$statuss = false;

// izveido savienojumu ar datubāzi
try {
    $connection = getConnection($database);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // iegūstu epastu un ziņu
        $email = trim($_POST['epasts']);
        $message = trim($_POST['zina']);

        // ielieku epastu un ziņu Message datubāzes tabulā
        insertMessage($connection, $email, $message);

        $statuss = true;
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
    <link rel="stylesheet" href="/style/contact.css">
    <link rel="stylesheet" href="/style/global.css">
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
        <?php if ($statuss == false): ?>
            <div id="formasLaukums">
                <h2>Iespēja kontaktēties ar mums nosūtot ziņu</h2>
                <form method="post" id="forma">
                    <span id="kluda"></span>
                    <div class="mb-3">
                        <label for="epasts">Epasts:</label>
                        <?php if (isset($_SESSION['lietotajvards'])): ?>
                            <input type="email" name="epasts" id="epasts" value="<?php echo $_SESSION['epasts'] ?>" required>
                        <?php else: ?>
                            <input type="email" name="epasts" id="epasts" required>
                        <?php endif ?>
                    </div>
                    <div class="mb-3">
                        <label for="zina">Ziņa: </label>
                        <textarea name="zina" id="zina" title="maksimāli 250 simboli" required></textarea>
                    </div>
                    <button type="submit" id="sutitZinu" class = "btn btn-primary" disabled>Aizsūtīt ziņu</button>
                </form>
            </div>
        <?php elseif ($statuss == true): ?>
            <div id="nosutits">
                <h2>Ziņa ir veikmīgi nosūtīta</h2>
                <P>Variet atgriezties uz sākumlapu.</P>
                <button id="atgriezties" class = "btn btn-primary" >Iet uz sākumlapu</button>
            </div>
        <?php endif ?>
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
<script src="/javascript/contact.js"></script>
</html>