import {stations} from "../storage/visual data/stations.js";

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

// Pievieno vizuālajā mapē stacijas
let i = 0;
while (i < stations.length) {
    let station = L.marker([stations[i].lat, stations[i].long]).addTo(visualMap);
    station.bindPopup(stations[i].sname);
    i = i + 1;
}