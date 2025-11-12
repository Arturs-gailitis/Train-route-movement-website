<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latvijas vilcienu maršrutu kustības portāls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style/main.css">
    <link rel="icon" type="image/svg+xml" href="/icons/websiteIcon.svg">
</head>
<body>
    <div class="galvene">
        <div class="nosaukums">
            <img src="/icons/websiteIcon.svg" alt="Portāla logo" id="logo">
            <h3 id="portālaNosaukums">Latvijas vilcienu maršrutu kustības portāls</h3>
        </div>

        <nav>
            <ul class="nav nav-pills" id="pogas">
                <li class="nav-item">
                    <a class="nav-link" href="#">Paziņojumi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Kontakti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><img src="/icons/shoppingCart.png" alt="Grozs" id="grozs"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><img src="/icons/noAccount.svg" alt="Lietotājs" id="lietotajs"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><img src="/icons/settings.svg" alt="Opcijas" id="opcijas"></a>
                </li>
            </ul>
        </nav>
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
                <li>Nopirkt konkrētā maršruta biļeti;</li>
                <li>Un citas izveidotās funkcijas.</li>
            </ul>

            <p>
                <a class="btn btn-primary" role="button" id="meklet">Sākt meklēt vilciena maršrutus</a>
            </p>
        </div>

        <div class="p-5 my-4" id="maršrutuMeklēšana">
            <h2 class="mb-4">Meklēšana</h2>
            <form>
                <label for="sākumstacija">Sākumstacija:</label>
                <select disabled name="sākumstacija">

                </select>
                <label for="beigustacija">Beigustacija:</label>
                <select disabled name="beigustacija">

                </select>
                <label for="datums">Datums:</label>
                <input type="date" name="datums">
                <input type="submit" value="Meklēt">
            </form>
        </div>

        <!-- paziņojumus būs jāievada šeit -->
        <div class="row marketing">
        </div>
    </div>
    <script src="/javascript/main.js"></script>
</body>
<footer class="mt-5 py-3">
    <p class="mb-0">© Latvijas vilcienu maršrutu kustības portāls 2025</p>
    <p class="mb-4" id="dati">
        Izmantotie dati: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv
        </a>
    </p>
</footer>
</html>
