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
            $error = "The username or email is incorrect. Please try again.";
        } else if (password_verify($password, $user['password']) == false) {
            $error = "The password is incorrect. Try again.";
        } else {
            // ieliek sesijā vajadzīgo informāciju
            $_SESSION['lietotajvards'] = $user['username'];
            $_SESSION['tiesibas'] = $user['rights'];
            $_SESSION['epasts'] = $user['email'];

            // nosūta uz sākumlapu
            header("Location: main.php");
            exit;
        }
    }

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
    <link rel="stylesheet" href="/style/login.css">
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
                        <a class="nav-link" href="datubaze.php">Database</a>
                    </li>
                <?php endif ?>
                <li class="nav-item">
                    <a class="nav-link" href="main.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pazinojumi.php">Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kontakti.php">Contact</a>
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
                        <a class = "profilaStatuss" href="registracija.php">Create account</a>
                    </li>
                <?php elseif (isset($_SESSION['lietotajvards'])): ?>
                    <li>
                        <a class = "profilaStatuss" id="iziesana" href="iziet.php">Log out</a>
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
                    <option value="http://localhost:8000/body/eng/login.php">English</option>
                    <option value="http://localhost:8000/body/lv/pieteikties.php">Latvian</option>
                </select>
            </div>

        </div>
    </div>
    <div id="pieteiksanas">
        <h2>Login form</h2>
        <span id="errors"><?php echo $error; ?></span>
        <form method="POST">
            <div class="mb-3">
                <label for="lietotajvardsEpasts">Username or email:</label>
                <input type="text" name="lietotajvardsEpasts" id="lietotajvardsEpasts" required>
            </div>
            <div class="mb-3">
                <label for="parole">Password:</label>
                <input type="password" name="parole" id="parole" required>
            </div>

            <button type="submit" id="pieteiksanasPoga">Login</button>
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
<script src="/javascript/login.js"></script>
<script src="/javascript/global.js"></script>
</html>