<?php

// saglabā sesiju
session_start();

require_once __DIR__ . '/../../db/initializeDB.php';
require_once __DIR__ . '/../../db/getingUsers.php';
$database = __DIR__ . '/../../storage/database/Users.sqlite';
$error = "";

// izveido savienojumu ar datubāzi
try {
    $connection = getConnection($database);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // ieg;ut lietotājvārdu, epastu, paroli un otru paroli
        $username = trim($_POST['lietotajvards']);
        $email = trim($_POST['epasts']);
        $password = $_POST['parole'];
        $confirmPassword = $_POST['atkartotaParole'];

        // pārbauda vai lietotājvārds, parole nav par mazu, vai abas paroles sakrīt, vai lietotājvārds un epasts jau tiek izmantots
        if (strlen($username) < 5) {
            $error = "Pārāk mazs lietotājvārds. Vajag vizmaz 5 rakstzīmju garumā.";
        } else if (strlen($password) < 8) {
            $error = "Pārāk maza parole. Vajag vizmaz 8 rakstzīmju garumā.";
        } else if ($password != $confirmPassword) {
            $error = "Abas paroles nav vienādas. Pamēģini vēlreiz.";
        } else if (checkUserByParam($connection, $username, "username" ) != false) {
            $error = "Šāds lietotājvārds jau pastāv. Izveido savādāku.";
        } else if (checkUserByParam($connection, $email, "email" ) != false) {
            $error = "Šāds epasts jau tiek izmantots. Ieliec citu epastu.";
        }

        if ($error == "") {
            // nohasho paroli
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // izveido lietotāju
            createUser($connection, $username, $email, $hashedPassword);

            // nosūta uz sākumlapu
            header("Location: sakumlapa.php");
            exit;
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
    <link rel="stylesheet" href="/style/register.css">
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
                    <option value="http://localhost:8000/body/lv/registracija.php">Latviešu</option>
                    <option value="http://localhost:8000/body/eng/register.php">Angļu</option>
                </select>
            </div>

        </div>
    </div>
    <div id="registresanas">
        <h2>Reģistrēšanās forma</h2>
        <span id="errors"><?php echo $error; ?></span>
        <form method="POST">
            <div class="mb-3">
                <label for="lietotajvards">Lietotājvārds:</label>
                <input type="text" name="lietotajvards" id="lietotajvards" required>
            </div>
            <div class="mb-3">
                <label for="epasts">Epasts:</label>
                <input type="email" name="epasts" id="epasts" required>
            </div>
            <div class="mb-3">
                <label for="parole">Parole:</label>
                <input type="password" name="parole" id="parole" required>
            </div>
            <div class="mb-3">
                <label for="atkartotaParole">Apstiprini paroli:</label>
                <input type="password" name="atkartotaParole" id="atkartotaParole" required>
            </div>

            <button type="submit" id="registracijasPoga">Reģistrējies</button>
        </form>
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
<script src="/javascript/register.js"></script>
</html>