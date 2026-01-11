import {stations} from "../storage/visual data/stations.js";

const footer = document.getElementById("footer");
const table = document.getElementById("tabula");
const map = document.getElementById("karte");

// pārbīda footer lai tas būtu ekrāna apakšā
document.addEventListener("DOMContentLoaded", () => {
    footer.style.position = "relative";

    if (table.offsetHeight < map.offsetHeight) {
        table.style.marginBottom = "450px";
    } else {
        table.style.marginBottom = "265px";
    }
})

// Izveido leaflet vizuālo mapi
const visualMap = L.map("karte").setView([56.880138, 24.606222], 7);

// Pievieno vizuālajā mapē OpenStreetMap
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(visualMap);

let stationsList = [];
let verifiedStations = [];

document.addEventListener("DOMContentLoaded", () => {
    const valueArea = document.querySelectorAll("tbody");

    // iegūst staciju nosaukumus kuri atrodās tabulā
    valueArea.forEach(record => {
        const tr = record.querySelectorAll("tr");

        tr.forEach(row => {
            const info = row.querySelector(".stacijas").innerText;
            stationsList.push(info);
        });
    });

    // iegūst staciju informāciju
    stationsList.forEach(station => {
        stations.forEach(stat => {
            if (station == stat.sname) {
                verifiedStations.push({
                    "station": stat.sname,
                    "lat": stat.lat,
                    "long": stat.long,
                    "color": stat.color,
                    "image": stat.image,
                });
            };
        });
    });

    // skatoties pēc staciju krāsas izvēlās atbilstošu ikonu
    let i = 0;
    let icons = [];
    let pictureLocation;

    while (i < verifiedStations.length) {

        if (verifiedStations[i].color == "#8bc540") {
            pictureLocation = "http://localhost:8000/icons/station-Icons/yellowGreenMarker.svg";
        } else if (verifiedStations[i].color == "#f8db22") {
            pictureLocation = "http://localhost:8000/icons/station-Icons/goldenYellowMarker.svg";
        } else if (verifiedStations[i].color == "#04abe8") {
            pictureLocation = "http://localhost:8000/icons/station-Icons/skyBlueMarker.svg";
        } else if (verifiedStations[i].color == "#ea6449") {
            pictureLocation = "http://localhost:8000/icons/station-Icons/corolRedMarker.svg";
        } else if (verifiedStations[i].color == "#bf9ac6") {
            pictureLocation = "http://localhost:8000/icons/station-Icons/lavenderPurpleMarker.svg";
        } else if (verifiedStations[i].color == "#000000") {
            pictureLocation = "http://localhost:8000/icons/station-Icons/blackMarker.svg";
        }

        icons.push(pictureLocation);
        i++;
    }

    // Pievieno vizuālajā mapē staciju ar ikonu un nosaukumu
    i = 0;

    while (i < verifiedStations.length) {

        // nokonfigurē stacijas ikonu
        let stationIcon = L.icon({
            iconUrl: icons[i],
            iconSize: [35, 38]
        })

        // ieliek kartē visas stacijas ar ikonu un tās nosaukumu papildus arī stacijas bildi
        let station = L.marker([verifiedStations[i].lat, verifiedStations[i].long], {icon: stationIcon}).addTo(visualMap);
        station.bindPopup(
            "<div align=center>" +
            "<h5><b>" + verifiedStations[i].station + "</b></h5><br>" +
            "<img src = " + verifiedStations[i].image + " width=315 hight=80>" +
            "</div>"
        );
        i = i + 1;
    }
});

const goBackButton = document.getElementById("iziet");

// Nospiežot šo pogu lietotājs tiks aizsūtīts atpakaļ
goBackButton.addEventListener("click", () => {
    const firstStation = document.getElementById("sakumaStacija").innerText;
    const lastStation = document.getElementById("beiguStacija").innerText;
    const date = document.getElementById("datums").innerText;
    let firstUrlStation;
    let lastUrlStation;

    // Pārvērš īpašos staciju nosaukumus lai tie atbilstu url
    if (firstStation == "Bieriņi/Bērnu slimnīca") {
        firstUrlStation = "Bieriņi%2FBērnu+slimnīca";
    } else if (firstStation == "Biznesa Augstskola Turība") {
        firstUrlStation = "Biznesa+Augstskola+Turība";
    } else if (firstStation == "Rēzekne II") {
        firstUrlStation = "Rēzekne+II";
    } else if (firstStation == "Tukums I") {
        firstUrlStation = "Tukums+I";
    } else if (firstStation == "Tukums II") {
        firstUrlStation = "Tukums+II";
    } else if (firstStation == "Vagonu Parks") {
        firstUrlStation = "Vagonu+Parks";
    } else {
        firstUrlStation = firstStation;
    }

    if (lastStation == "Bieriņi/Bērnu slimnīca") {
        lastUrlStation = "Bieriņi%2FBērnu+slimnīca";
    } else if (lastStation == "Biznesa Augstskola Turība") {
        lastUrlStation = "Biznesa+Augstskola+Turība";
    } else if (lastStation == "Rēzekne II") {
        lastUrlStation = "Rēzekne+II";
    } else if (lastStation == "Tukums I") {
        lastUrlStation = "Tukums+I";
    } else if (lastStation == "Tukums II") {
        lastUrlStation = "Tukums+II";
    } else if (lastStation == "Vagonu Parks") {
        lastUrlStation = "Vagonu+Parks";
    } else {
        lastUrlStation = lastStation;
    }

    const url = "marsruti.php?sākumstacija="+ firstUrlStation + "&beigustacija=" + lastUrlStation + "&datums=" + date;

    // lietotājs tiek aizsūtīs uz šo url
    window.location.href = url;

})