import {stations} from "../storage/visual data/stations.js";
import { 
    railtrack, valgaStations, valgaRoutes, skulteStations, skulteRoutes, tukumsStations, tukumsRoutes, liepajaStations, 
    liepajaRoutes, gulbeneStations, gulbeneRoute, indraStations, indraRoute, zilupeStations, zilupeRoute
} from "../storage/visual data/railways/unite.js";

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
    // Iegūst visus url get metodes vērtības
    const getParam = new URLSearchParams(window.location.search);

    // Paņem tikai konkrētās vērtības
    const firstStation = getParam.get("altstart");
    const lastStation = getParam.get("altend");

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

    // izveido pareizu url
    const urlGet = "?sākumstacija="+ firstUrlStation + "&beigustacija=" + lastUrlStation + "&datums=" + date;
    let url = "";

    if (window.location.href.includes("/lv/")) {
        url = "marsruti.php" + urlGet;
    } else {
        url = "movement.php" + urlGet;
    }

    // lietotājs tiek aizsūtīs uz šo url
    window.location.href = url;

})

const startStation = document.getElementById("sakumaStacija").innerText;
const endStation = document.getElementById("beiguStacija").innerText;

// Funkkcija kas iegūst un ieliek dzelzceļa ceļu vizuālajā kartē
function getTracks(stationArray, routeArray, railColor) {

    let startIndex = 0;
    let endIndex = 0;

    // Iegūst sākuma stacijas un beigu staciju indeksus 
    for (let i = 0; i < stationArray.length; i++) {
        if (stationArray[i] == startStation) {
            startIndex = i;
        }

        if (stationArray[i] == endStation) {
            endIndex = i;
        }
    }

    let newStartIndex = 0;
    let newEndIndex = 0;

    // Pārmaina indeksus otrādāk, skatoties kādā virzienā maršruts ir
    if (startIndex <= endIndex) {
        newStartIndex = startIndex;
        newEndIndex = endIndex;
    } else {
        newStartIndex = endIndex;
        newEndIndex = startIndex;
    }

    let correctStations = [];

    // Atrod visas stacijas kas atbilst starp sākuma un beigu stacijām
    for (let i = newStartIndex; i <= newEndIndex; i++) {
        correctStations.push(stationArray[i]);
    }

    let correctRoutes = [];

    // Atrod visas atbilstošās dzelzceļa posmus
    for (let i = 0; i < correctStations.length - 1; i++) {
        routeArray.forEach(route => {
            if (route.startsWith(correctStations[i] + " - ") || route == correctStations[i]) {
                
                if (correctRoutes.includes(route) == false) {
                    correctRoutes.push(route);
                }
            }
        }) 
    }

    let correctRigaRoutes = [];
    const deafaultRigaRoutes = ["Rīga - Torņakalns", "Rīga - Zemitāni", "Savieno Zemitānus", "Savieno Torņakalnu",
        "Rīga - Vagonu parks"
    ];
    let filteredRoutes = [];

    // filtē dzelceļa posmus skatoties vai konkrētais dzelzceļa posms ir Rīgas apgabals
    correctRoutes.forEach(route => {
        if (deafaultRigaRoutes.includes(route)) {
            correctRigaRoutes.push(route);
        } else {
            filteredRoutes.push(route);
        }
    });

    correctRoutes = filteredRoutes;

    let fullTrack = []
    let fullRIgaTrack = []

    // skatās vai izfiltētais pārējās daļas dzelzceļa maršruta masīvs nav tukšs
    if (correctRoutes.length > 0) {

        // Atrod visu pārējās daļas masīva elementu GeoJSON informāciju
        railtrack.forEach(rail => {
            const railInfo =  {
                ...rail,
                features: rail.features.filter(f =>
                    correctRoutes.includes(f.properties.railways)
                )
            };

            if (railInfo.features.length > 0) {
                fullTrack.push(railInfo);
            }
        });

        // ieliek ceļu vizuālajā kartē
        fullTrack.forEach(track => {

            const railway = L.geoJSON(track, {
                style: {
                    color: railColor,
                    weight: 10
                }
            })

            railway.addTo(visualMap);
        })

    }

    // skatās vai izfiltētais Rīgas daļas dzelzceļa maršruta masīvs nav tukšs
    if (correctRigaRoutes.length > 0) {

        // Atrod visu Rīgas daļas masīva elementu GeoJSON informāciju 
        railtrack.forEach(rail => {
            const railInfo =  {
                ...rail,
                features: rail.features.filter(f =>
                    correctRigaRoutes.includes(f.properties.railways)
                )
            };

            if (railInfo.features.length > 0) {
                fullRIgaTrack.push(railInfo);
            }
        });

        // ieliek ceļu vizuālajā kartē
        fullRIgaTrack.forEach(track => {

            const railway = L.geoJSON(track, {
                style: {
                    color: "black",
                    weight: 10
                }
            })

            railway.addTo(visualMap);
        })
    }

}

document.addEventListener("DOMContentLoaded", () => {
    
    // Ieliek atbilstošo ceļu vizuālajā kartē
    if (valgaStations.includes(startStation) && valgaStations.includes(endStation)) {
        getTracks(valgaStations, valgaRoutes, "#8bc540");
    } else if (skulteStations.includes(startStation) && skulteStations.includes(endStation)) {
        getTracks(skulteStations, skulteRoutes, "#f8db22");
    } else if (tukumsStations.includes(startStation) && tukumsStations.includes(endStation)) {
        getTracks(tukumsStations, tukumsRoutes, "#04abe8");
    } else if (liepajaStations.includes(startStation) && liepajaStations.includes(endStation)) {
        getTracks(liepajaStations, liepajaRoutes, "#ea6449");
    } else if (gulbeneStations.includes(startStation) && gulbeneStations.includes(endStation)) {
        getTracks(gulbeneStations, gulbeneRoute, "#bf9ac6");
    } else if (indraStations.includes(startStation) && indraStations.includes(endStation)) {
        getTracks(indraStations, indraRoute, "#bf9ac6");
    } else if (zilupeStations.includes(startStation) && zilupeStations.includes(endStation)) {
        getTracks(zilupeStations, zilupeRoute, "#bf9ac6");
    }
})