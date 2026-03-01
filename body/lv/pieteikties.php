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
        
        // iegūst paroli, lietotājvārdu vai epastu no šīs lapas formas
        $usernameOrEmail = trim($_POST['lietotajvardsEpasts']);
        $password = $_POST['parole'];

        // no dotajiem formas parametriem iegūst infromāciju par lietotāju 
        $user = getUser($connection, $usernameOrEmail);

        // validē vai lietotājs eksistē un pārbauda vai abas paroles sakrīt
        if ($user == false) {
            $error = "Lietotājvārds vai epasts ir nepareizs. Mēģini vēlreiz.";
        } else if (password_verify($password, $user['password']) == false) {
            $error = "Parole ir nepareiza. Mēģini vēlreiz";
        } else {
            // ieliek sesijā vajadzīgo informāciju
            $_SESSION['lietotajvards'] = $user['username'];
            $_SESSION['tiesibas'] = $user['rights'];
            $_SESSION['epasts'] = $user['email'];

            // nosūta uz sākumlapu
            header("Location: sakumlapa.php");
            exit;
        }
    }

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
    <link rel="stylesheet" href="/style/login.css">
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
                    <option value="http://localhost:8000/body/lv/pieteikties.php">Latviešu</option>
                    <option value="http://localhost:8000/body/eng/login.php">Angļu</option>
                </select>
            </div>

        </div>
    </div>
    <div id="pieteiksanas">
        <h2>Pieteikšanās forma</h2>
        <span id="errors"><?php echo $error; ?></span>
        <form method="POST">
            <div class="mb-3">
                <label for="lietotajvardsEpasts">Lietotājvārds vai epasts:</label>
                <input type="text" name="lietotajvardsEpasts" id="lietotajvardsEpasts" required>
            </div>
            <div class="mb-3">
                <label for="parole">Parole:</label>
                <input type="password" name="parole" id="parole" required>
            </div>

            <button type="submit" id="pieteiksanasPoga">Pieteikties</button>
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
<script src="/javascript/login.js"></script>
<script src="/javascript/global.js"></script>
</html>