<?php

// saglabā sesiju
session_start();

?>
<!DOCTYPE html>
<html lang="eng">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvian Train Route Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/main.css">
    <link rel="stylesheet" href="/style/global.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
                    <option value="http://localhost:8000/body/eng/mainPage.php">English</option>
                    <option value="http://localhost:8000/body/lv/sakumlapa.php">Latvian</option>
                </select>
            </div>

        </div>
    </div>
    <div class="container-fluid">
        
        <div class="p-5 my-4" id="galvenaSekcija">

            <img src="/icons/trainView.jpg" id="vilcienaSkats">

            <h1 class="display-4" id="sveiciens">Welcome to the Latvian Train Route Portal</h1>

            <p class="mb-4" id="apraksts">
                The Latvian Train Route Portal is an online solution where the required data is taken from
                Latvia’s Open Data Portal. <br> See the source link in the footer.
            </p>

            <h2 class="mt-4" id="funkcijuSākums">On this portal, the user can:</h2>

            <ul class="mb-4" id ="funkcijas">
                <li>Search for a train route by start and end stops, specifying a date;</li>
                <li>Get additional information about the train route;</li>
                <li>View route status and notifications in real time;</li>
                <li>See the route path and station visualization on an interactive map;</li>
                <li>Buy a ticket for the selected route.</li>
            </ul>

            <p>
                <a class="btn btn-primary" role="button" id="meklet">Start searching for train routes</a>
            </p>
        </div>

        <div class="p-5 my-4" id="maršrutuMeklēšana">

            <img src="/icons/trainView.jpg" id="vilcienaSkats">

            <div class= "p-2 mx-4" id="maršrutuMeklēšanasSadaļa">
                <button type="button" class="btn btn-secondary" id="atcelt" title="Close search section">
                    <img src="/icons/cross.svg" alt="Cancel" id="atceltIcona">
                </button>

                <h2 class="mb-4" id="meklesanasTituls">Search</h2>
                <form id="meklesanasForma" method="get" action="movement.php">
                    <div class="mb-3">
                        <label for="sākumstacija">Start station:</label>
                        <input type="text" class="form-control" name="sākumstacija" id="sākumstacija" required>
                    </div>

                    <div class="mb-3">
                        <label for="beigustacija">End station:</label>
                        <input type="text" class="form-control" name="beigustacija" id="beigustacija" required>
                    </div>

                    <div class="mb-3">
                        <label for="datums">Date:</label>
                        <input type="date" class="form-control" name="datums" id="datums" required>
                    </div>

                    <input type="submit" value="Search" class="btn btn-primary" id ="meklet">
                </form>
            </div>
        </div>
    </div>
    <div id="vizuālāMapesLaukums" class = "container-fluid">
        <h1 id="vilcienaMapesVirsraksts">Interactive Train Traffic Map</h1>
        <div>
            <button type="button" id="atvertFiltresanuPoga" title="Open route filtering section">
                <img src="/icons/arrow-down.svg" alt="Open route filtering section" id="atvertFiltresanuIkona">
            </button>
            <div id=filtresanasSadala>
                <h3 id="filtresanasTituls">Route path filtering</h3>
                <ul>
                    <li><label><input type="checkbox" id="Tukums" checked> - Tukums II : Torņakalns</label></li>
                    <li><label><input type="checkbox" id="Liepaja" checked> - Liepāja : Torņakalns</label></li>
                    <li><label><input type="checkbox" id="Skulte" checked> - Skulte : Zemitāni</label></li>
                    <li><label><input type="checkbox" id="Valga" checked> - Valga : Zemitāni</label></li>
                    <li><label><input type="checkbox" id="Latgale" checked> - Indra, Zilupe, Gulbene : Rīga</label></li>
                    <li><label><input type="checkbox" id="Riga" checked> - Torņakalns : Zemitāni</label></li>
                </ul>
            </div>
        </div>
        <div id="vilcienaMape"></div>
    </div>
</body>
<footer class="mt-5 py-3">
    <p class="mb-0">© Latvian Train Route Portal <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Data used: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv </a> <br> Loaded: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/javascript/global.js"></script>
<script type="module" src="/javascript/main.js"></script>
</html>
