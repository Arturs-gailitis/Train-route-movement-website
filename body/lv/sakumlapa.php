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
<header class="py-3 mb-4 border-bottom">
    <div class="d-flex flex-wrap align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <img src="/icons/websiteIcon.svg" alt="Portāla logo" id="logo">
            <h3 class="text-muted mb-0">Latvijas vilcienu maršrutu kustības portāls</h3>
        </div>

        <nav>
            <ul class="nav nav-pills">
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
            </ul>
        </nav>
    </div>
</header>
<body>
    <div class="container-fluid">
        
        <div class="bg-light p-5 rounded text-center my-4 position-relative">

            <img src="/icons/trainView.jpg" id="vilcienaSkats">

            <h1 class="display-4 fw-bold">Sveicināti Latvijas vilcienu maršrutu kustības portālā</h1>

            <p class="lead mb-4">
                Latvijas vilcienu maršrutu kustības portāls ir tiešsaistes risinājums, kur nepieciešamie dati tiek ņemti no
                Latvijas atvērtā datu portāla. <br> Skatīt avotu lapas kājenē.
            </p>

            <h2 class="h4 fw-bold mt-4">Šajā portālā lietotājs var:</h2>

            <ul class="text-start lead mx-auto mb-4" id ="funkcijas">
                <li>Meklēt vilciena maršrutu pēc sākuma un beigu pieturām, norādot datumu;</li>
                <li>Iegūt papildus informāciju par vilciena maršrutu;</li>
                <li>Reāllaikā uzzināt maršruta statusu un paziņojumus;</li>
                <li>Skatīt konkrētā maršruta ceļu un staciju vizualizāciju interaktīvā kartē;</li>
                <li>Nopirkt konkrētā maršruta biļeti;</li>
                <li>Un citas izveidotās funkcijas.</li>
            </ul>

            <p>
                <a class="btn btn-success btn-lg" href="#" role="button" id="meklet">Sākt meklēt vilciena maršrutus</a>
            </p>
        </div>

        <!-- paziņojumus būs jāievada šeit -->
        <div class="row marketing">
        </div>
    </div>
</body>
<footer class="footer mt-5 py-3 border-top">
    <p class="mb-0 text-center">© Latvijas vilcienu maršrutu kustības portāls 2025</p>
    <p class="mb-4">
        Izmantotie dati: <a href="https://data.gov.lv/dati/lv/dataset/iekszemes-dzelzcela-vilcienu-kustibas-saraksts-gtfs-formata">
            data.gov.lv
        </a>
    </p>
</footer>
</html>
