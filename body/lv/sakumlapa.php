<?php

// saglabā sesiju
session_start();

?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvijas vilcienu maršrutu kustības portāls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/main.css">
    <link rel="stylesheet" href="/style/global.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
                    <option value="http://localhost:8000/body/lv/sakumlapa.php">Latviešu</option>
                    <option value="http://localhost:8000/body/eng/main.php">Angļu</option>
                </select>
            </div>

        </div>
    </div>
    <div class="container-fluid">
        
        <div class="p-5 my-4" id="galvenaSekcija">

            <img src="/icons/trainView.jpg" id="vilcienaSkats">

            <h1 class="display-4" id="sveiciens">Sveicināti Latvijas vilcienu maršrutu kustības portālā</h1>

            <p class="mb-4" id="apraksts">
                Latvijas vilcienu maršrutu kustības portāls ir tiešsaistes risinājums, kur nepieciešamie dati tiek ņemti no
                Latvijas atvērtā datu portāla. <br> Skatīt avotu lapas kājenē.
            </p>

            <h2 class="mt-4" id="funkcijuSākums">Šajā portālā lietotājs var:</h2>

            <ul class="mb-4" id ="funkcijas">
                <li>Meklēt vilciena maršrutu pēc sākuma un beigu pieturām, norādot datumu;</li>
                <li>Iegūt papildus informāciju par vilciena maršrutu;</li>
                <li>Reāllaikā uzzināt maršruta statusu un paziņojumus;</li>
                <li>Skatīt konkrētā maršruta ceļu un staciju vizualizāciju interaktīvā kartē;</li>
                <li>Nopirkt konkrētā maršruta biļeti.</li>
            </ul>

            <p>
                <a class="btn btn-primary" role="button" id="meklet">Sākt meklēt vilciena maršrutus</a>
            </p>
        </div>

        <div class="p-5 my-4" id="maršrutuMeklēšana">

            <img src="/icons/trainView.jpg" id="vilcienaSkats">

            <div class= "p-2 mx-4" id="maršrutuMeklēšanasSadaļa">
                <button type="button" class="btn btn-secondary" id="atcelt" title="Aizvērt meklēšanas sadaļu">
                    <img src="/icons/cross.svg" alt="Atcelt" id="atceltIcona">
                </button>

                <h2 class="mb-4" id="meklesanasTituls">Meklēšana</h2>
                <form id="meklesanasForma" method="get" action="marsruti.php">
                    <div class="mb-3">
                        <label for="sākumstacija">Sākuma stacija:</label>
                        <input type="text" class="form-control" name="sākumstacija" id="sākumstacija" required>
                    </div>

                    <div class="mb-3">
                        <label for="beigustacija">Beigu stacija:</label>
                        <input type="text" class="form-control" name="beigustacija" id="beigustacija" required>
                    </div>

                    <div class="mb-3">
                        <label for="datums">Datums:</label>
                        <input type="date" class="form-control" name="datums" id="datums" required>
                    </div>

                    <input type="submit" value="Meklēt" class="btn btn-primary" id ="Meklet">
                </form>
            </div>
        </div>
    </div>
    <div id="vizuālāMapesLaukums" class = "container-fluid">
        <h1 id="vilcienaMapesVirsraksts">Vilcienu kustības interaktīva karte</h1>
        <div>
            <button type="button" id="atvertFiltresanuPoga" title="Atvērt maršrutu filtrēšanas sadaļu">
                <img src="/icons/arrow-down.svg" alt="Atvērt maršrtuta filtēšanas sadaļu" id="atvertFiltresanuIkona">
            </button>
            <div id=filtresanasSadala>
                <h3 id="filtresanasTituls">Maršrutu ceļu filtrēšana</h3>
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
    <p class="mb-0">© Latvijas vilcienu maršrutu kustības portāls <span id=projektaGads></span></p>
    <p class="mb-4" id="dati">
        Izmantotie dati: <a href="https://www.vivi.lv/lv/sadarbiba/atvertie-dati/">
            vivi.lv </a> <br> Ielādēts: <span id="ielādesDatums"></span>
    </p>
</footer>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/javascript/global.js"></script>
<script type="module" src="/javascript/main.js"></script>
</html>
