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

const filterButton = document.getElementById("atvertFiltresanuPoga");
const filterBox = document.getElementById("filtresanasSadala");
const filterButtonIcon = document.getElementById("atvertFiltresanuIkona");
let statuss = false;

// Atver vai aizver maršruta filtrēšanas sadaļu
filterButton.addEventListener("click", () => {
    if (statuss == false) {
        filterBox.style.display = "block";
        filterBox.style.height = "auto";
        filterButtonIcon.src = "http://localhost:8000/icons/arrow-up.svg";
        filterButton.title = "Aizvērt maršrutu filtrēšanas sadaļu"
        filterButton.style.marginBottom = "5px"
        statuss = true;
    } else {
        filterBox.style.display = "none";
        filterBox.style.height = "0px";
        filterButtonIcon.src = "http://localhost:8000/icons/arrow-down.svg";
        filterButton.title = "Atvērt maršrutu filtrēšanas sadaļu";
        filterButton.style.marginBottom = "20px"
        statuss = false;
    }
})

// Izveido leaflet vizuālo mapi
const visualMap = L.map("vilcienaMape").setView([56.880138, 24.606222], 7);

// Pievieno vizuālajā mapē OpenStreetMap
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(visualMap);

let i = 0;
let pictureLocation;

// izveido staciju un dzelzceļa ceļa grupas
let TukumsStationLayer = L.layerGroup();
let LiepajaStationLayer = L.layerGroup();
let RigaStationLayer = L.layerGroup();
let SkulteStationLayer = L.layerGroup();
let ValgaStationLayer = L.layerGroup();
let LatgaleStationLayer = L.layerGroup();

let TukumsRailLayer = L.layerGroup();
let LiepajaRailLayer = L.layerGroup();
let RigaRailLayer = L.layerGroup();
let SkulteRailLayer = L.layerGroup();
let ValgaRailLayer = L.layerGroup();
let LatgaleRailLayer = L.layerGroup();

// Funkcija kas ieliks staciju grupā
function newStation(url, stations, layer, i) {
    // nokonfigurē stacijas ikonu
    let stationIcon = L.icon({
        iconUrl: url,
        iconSize: [35, 38]
    })

    // ieliek kartē visas stacijas ar ikonu un tās nosaukumu papildus arī stacijas bildi
    let station = L.marker([stations[i].lat, stations[i].long], {icon: stationIcon});
    station.bindPopup(
        "<div align=center>" +
        "<h5><b>" + stations[i].sname + "</b></h5><br>" +
        "<img src = " + stations[i].image + " width=315 hight=80>" +
        "</div>"
    );

    // ieliek staciju grupā
    layer.addLayer(station)
}

while (i < stations.length) {

    // skatoties pēc norādītās staciju krāsas izvēlās ikonu un ieliek staciju grupā
    if (stations[i].color == "#8bc540") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/yellowGreenMarker.svg";
        newStation(pictureLocation, stations, ValgaStationLayer, i);
    } else if (stations[i].color == "#f8db22") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/goldenYellowMarker.svg";
        newStation(pictureLocation, stations, SkulteStationLayer, i);
    } else if (stations[i].color == "#04abe8") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/skyBlueMarker.svg";
        newStation(pictureLocation, stations, TukumsStationLayer, i);
    } else if (stations[i].color == "#ea6449") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/corolRedMarker.svg";
        newStation(pictureLocation, stations, LiepajaStationLayer, i);
    } else if (stations[i].color == "#bf9ac6") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/lavenderPurpleMarker.svg";
        newStation(pictureLocation, stations, LatgaleStationLayer, i);
    } else if (stations[i].color == "#000000") {
        pictureLocation = "http://localhost:8000/icons/station-Icons/blackMarker.svg";
        newStation(pictureLocation, stations, RigaStationLayer, i);
    }

    i++;
}

let trackColor;

// funkcija kas ieliks dzelzceļa līniju grupā
function newRailway(trackColor, RailLayer, rail) {
    // izveido dzelzceļa objektu
    let railway = L.geoJSON(rail, {
        style: {
            color: trackColor,
            weight: 10
        }
    });

    // ieliek dzelzceļu grupā
    RailLayer.addLayer(railway);
}

// Ieliek interaktīvajā kartē dzelzceļa posmus
railtrack.forEach(rail => {

    // Nosaka kāda krāsa būs katram dzelzceļa posmam
    if (rail.features[0].properties.railways == "Zvejniekciems - Skulte") {
        trackColor = "#f8db22";
    } else if (rail.features[0].properties.railways == "Tukums I - Tukums II") {
        trackColor = "#04abe8";
    } else if ((rail.features[0].properties.railways == "Skrunda - Liepāja") || 
    (rail.features[0].properties.railways == "Saldus - Skrunda") || 
    (rail.features[0].properties.railways == "Jelgava - Dobele")) {
        trackColor = "#ea6449";
    } else if ((rail.features[0].properties.railways == "Vagonu parks - Jāņavārti") || 
    (rail.features[0].properties.railways == "Krustpils - Trepe") || 
    (rail.features[0].properties.railways == "Krustpils - Kūkas") || 
    (rail.features[0].properties.railways == "Pļaviņas - Jaunkalsnava")) {
        trackColor = "#bf9ac6";
    } else if (rail.features[0].properties.railways == "Čiekurkalns - Šmerlis") {
        trackColor = "#8bc540";
    } else if (rail.features[0].properties.railways == "Rīga - Torņakalns") {
        trackColor = "#000000";
    }

    // pēc krāsas izveido dzelzceļa objektu un ieliek atbilstošā grupā
    if (trackColor == "#f8db22") {
        newRailway(trackColor, SkulteRailLayer, rail);
    } else if (trackColor == "#04abe8") {
        newRailway(trackColor, TukumsRailLayer, rail);
    } else if (trackColor == "#ea6449") {
        newRailway(trackColor, LiepajaRailLayer, rail);
    } else if (trackColor == "#bf9ac6") {
        newRailway(trackColor, LatgaleRailLayer, rail);
    } else if (trackColor == "#8bc540") {
        newRailway(trackColor, ValgaRailLayer, rail);
    } else if (trackColor == "#000000") {
        newRailway(trackColor, RigaRailLayer, rail);
    }

})

const Tukums = document.getElementById("Tukums");
const Liepaja = document.getElementById("Liepaja");
const Skulte = document.getElementById("Skulte");
const Valga = document.getElementById("Valga");
const Latgale = document.getElementById("Latgale");
const Riga = document.getElementById("Riga");

// Kā orģināli visas staciju un dzelzceļa grupas liek iekšā kartē
document.addEventListener("DOMContentLoaded", () => {
    TukumsStationLayer.addTo(visualMap);
    TukumsRailLayer.addTo(visualMap);

    LiepajaStationLayer.addTo(visualMap);
    LiepajaRailLayer.addTo(visualMap);
    
    RigaStationLayer.addTo(visualMap);
    RigaRailLayer.addTo(visualMap);

    SkulteStationLayer.addTo(visualMap);
    SkulteRailLayer.addTo(visualMap);

    ValgaStationLayer.addTo(visualMap);
    ValgaRailLayer.addTo(visualMap);

    LatgaleRailLayer.addTo(visualMap);
    LatgaleStationLayer.addTo(visualMap);
});

// atbild par Tukums II - Torņakalns dzelceļa filtrēšanu
Tukums.addEventListener("change", e => {
    if (e.target.checked != true) {
        visualMap.removeLayer(TukumsStationLayer);
        visualMap.removeLayer(TukumsRailLayer);
    } else {
        TukumsStationLayer.addTo(visualMap);
        TukumsRailLayer.addTo(visualMap);
    }
});

// atbild par Liepāja - Torņakalns dzelceļa filtrēšanu
Liepaja.addEventListener("change", e => {
    if (e.target.checked != true) {
        visualMap.removeLayer(LiepajaStationLayer);
        visualMap.removeLayer(LiepajaRailLayer);
    } else {
        LiepajaStationLayer.addTo(visualMap);
        LiepajaRailLayer.addTo(visualMap);
    }
});

// atbild par Torņakalns - Zemitāni dzelceļa filtrēšanu
Riga.addEventListener("change", e => {
    if (e.target.checked != true) {
        visualMap.removeLayer(RigaStationLayer);
        visualMap.removeLayer(RigaRailLayer);
    } else {
        RigaStationLayer.addTo(visualMap);
        RigaRailLayer.addTo(visualMap);
    }
});

// atbild par Skulte - Zemitāni dzelceļa filtrēšanu
Skulte.addEventListener("change", e => {
    if (e.target.checked != true) {
        visualMap.removeLayer(SkulteStationLayer);
        visualMap.removeLayer(SkulteRailLayer);
    } else {
        SkulteStationLayer.addTo(visualMap);
        SkulteRailLayer.addTo(visualMap);
    }
});

// atbild par Valga - Zemitāni dzelceļa filtrēšanu
Valga.addEventListener("change", e => {
    if (e.target.checked != true) {
        visualMap.removeLayer(ValgaStationLayer);
        visualMap.removeLayer(ValgaRailLayer);
    } else {
        ValgaStationLayer.addTo(visualMap);
        ValgaRailLayer.addTo(visualMap);
    }
});

// atbild par Indra, Zilupe, Gulbene - Rīga dzelceļa filtrēšanu
Latgale.addEventListener("change", e => {
    if (e.target.checked != true) {
        visualMap.removeLayer(LatgaleStationLayer);
        visualMap.removeLayer(LatgaleRailLayer);
    } else {
        LatgaleStationLayer.addTo(visualMap);
        LatgaleRailLayer.addTo(visualMap);
    }
});