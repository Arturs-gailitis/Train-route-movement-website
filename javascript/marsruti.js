import {stations} from "../storage/visual data/stations.js";

const button = document.getElementById("atvērtMeklšanuPoga");
const box = document.getElementById("maršrutuMeklēšanasSadaļa");
const boxButton = document.getElementById("atcelt");
const footer = document.querySelector("footer");
const table = document.getElementById("marsrutuTabula");

// Ielādē footer 250 pikseļus no maršruta tabulas
document.addEventListener("DOMContentLoaded", () => {
    footer.style.position = "relative";
    table.style.marginBottom = "200px";
})

// Parāda maršruta meklēšanas sadaļu
button.addEventListener("click", () => {
    box.style.display = "block";
    button.style.display = "none";
    table.style.marginBottom = "200px";
    footer.style.position = "relative";
});

// Aizver maršruta meklēšanas sadaļu
boxButton.addEventListener("click", () => {
    box.style.display = "none";
    button.style.display = "block";
    table.style.marginBottom = "200px";
});

// automātiski ieliek url linku uz 1188.lv
document.addEventListener("DOMContentLoaded", () => {
    const buyButtons = document.querySelectorAll(".pirktPogas");
    const infoButtons = document.querySelectorAll(".infoPogas");
    const date = document.getElementById("datumaInfo").innerText;

    let startStations = [];
    let endStations = [];

    // Iegūst nepieciešamo sākumstaciju un beigustaciju no infoButtons
    for (let i = 0; i < infoButtons.length; i++) {
        // iegūst url un tās daļu, kura mainās pēc GET metodes
        const url =  infoButtons[i].href;
        const querry = url.split("?")[1];

        // dabū vajadzīgās url sadaļas
        const startStation = querry.split("&")[1];
        const endStation = querry.split("&")[2];

        // Pēc staciju iegūšanas pārtaisi cilvēka saprotamā tekstā
        const startStationValue = decodeURIComponent(startStation.split("=")[1]);
        const endStationValue = decodeURIComponent(endStation.split("=")[1]);

        startStations.push(startStationValue);
        endStations.push(endStationValue);  
    }

    for (let i = 0; i < buyButtons.length; i++) {
        const startStation = startStations[i];
        const endStation = endStations[i];

        let startStationAlt = "";
        let startStationNumber = null;
        let endStationAlt = "";
        let endStationNumber = null;

        // iegūst nepieciešamo info priekš 1188.lv url
        stations.forEach(stat => {
            if (startStation == stat.sname) {
                startStationAlt = stat.alt;
                startStationNumber = stat.code;
            }

            if (endStation == stat.sname) {
                endStationAlt = stat.alt;
                endStationNumber = stat.code;
            }
        })

        // izveido 1188.lv url un ieliek viņu konkrētajā buyButtons
        const websiteURL = "https://www.1188.lv/satiksme/vilcieni/" + startStationAlt + "/" + endStationAlt + "/" + startStationNumber +"/" + endStationNumber + "/diena/" + date;
        buyButtons[i].href = websiteURL;
    
    }
})