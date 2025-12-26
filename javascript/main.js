import {stations} from "../storage/visual data/stations.js";
import { railtrack } from "../storage/visual data/railways/unite.js";

const button = document.getElementById("meklet");
const box = document.getElementById("galvenaSekcija");
const meklesana = document.getElementById("maršrutuMeklēšana");
const iziet = document.getElementById("atcelt");
const garums = box.offsetHeight;

// Nomaina sākumlapas sākuma daļu uz meklēšanas sadaļu
button.addEventListener("click", () => {
    box.style.display = "none";
    meklesana.style.display = "flex";
    meklesana.style.height = `${garums}px`;
});

// Nomaina meklēšanas sadaļas daļu uz sākuma daļu
iziet.addEventListener("click", () => {
    meklesana.style.display = "none";
    box.style.display = "block";
});

// Izveido leaflet vizuālo mapi
const visualMap = L.map("vilcienaMape").setView([56.880138, 24.606222], 7);

// Pievieno vizuālajā mapē OpenStreetMap
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(visualMap);

// skatoties pēc staciju krāsas izvēlās atbilstošu ikonu
let i = 0;
let icons = [];
let pictureLocation;

while (i < stations.length) {

    if (stations[i].color == "#8bc540") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/yellowGreenMarker.svg";
    } else if (stations[i].color == "#f8db22") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/goldenYellowMarker.svg";
    } else if (stations[i].color == "#04abe8") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/skyBlueMarker.svg";
    } else if (stations[i].color == "#ea6449") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/corolRedMarker.svg";
    } else if (stations[i].color == "#bf9ac6") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/lavenderPurpleMarker.svg";
    } else if (stations[i].color == "#000000") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/blackMarker.svg";
    }

    icons.push(pictureLocation);
    i++;
}

// Pievieno vizuālajā mapē staciju ar ikonu un nosaukumu
i = 0;

while (i < stations.length) {

    // nokonfigurē stacijas ikonu
    let stationIcon = L.icon({
        iconUrl: icons[i],
        iconSize: [45, 48]
    })

    // ieliek kartē visas stacijas ar ikonu un tās nosaukumu
    let station = L.marker([stations[i].lat, stations[i].long], {icon: stationIcon}).addTo(visualMap);
    station.bindPopup(stations[i].sname);
    i = i + 1;
}

let trackColor;

// Ieliek interaktīvajā kartē dzelzceļa posmus
railtrack.forEach(rail => {

    // Nosaka kāda krāsa būs katram dzelzceļa posmam
    if (rail.features[0].properties.railways == "Skulte - Zvejniekciems") {
        trackColor = "#f8db22";
    } else if (rail.features[0].properties.railways == "Tukums II - Tukums I") {
        trackColor = "#04abe8";
    } else if ((rail.features[0].properties.railways == "Liepāja - Skrunde") || 
    (rail.features[0].properties.railways == "Skrunde - Liepāja") || 
    (rail.features[0].properties.railways == "Dobele - Jelgava")) {
        trackColor = "#ea6449";
    } else if ((rail.features[0].properties.railways == "Vagonu parks - Jāņavārti") || 
    (rail.features[0].properties.railways == "Krustpils - Trepe") || 
    (rail.features[0].properties.railways == "Krustpils - Kūkas")) {
        trackColor = "#bf9ac6";
    }

    // ieliek interaktīvajā kartē dzelzceļa posmu
    L.geoJSON(rail, {
            style: {
                color: trackColor,
                weight: 10
            }
    }).addTo(visualMap);
})