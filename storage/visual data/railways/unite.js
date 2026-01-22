import { bToS } from "./BrasaSkulteRailways.geoJson.js";
import { tToZ } from "./TukumsZasulauksRailways.geoJson.js";
import { lToS } from "./LiepājaSkrundeRailways.js";
import { sToD } from "./SkrundeDobeleRailways.js";
import { dToB } from "./DobeleBieriniRailways.js";
import { vToP } from "./VagonuparksPlavinasRailways.js";
import { pToI } from "./PlavinasIndraRailwats.js";
import { kToZ } from "./KrustpilsZilupeRailways.js";
import { pToG } from "./PlavinasGulbeneRailways.js";
import { cToV } from "./CiekurkalnsValgaRailways.js";
import { tornToZ } from "./TornkalnsZemitaniRailways.js";

// apvieno visus dzelzceļa posmus vienā masīvā
export const railtrack = [bToS, tToZ, lToS, sToD, dToB, vToP, pToI, kToZ, pToG, cToV, tornToZ];

// Dabū visus posma nosaukumus no TornkalnsZemitaniRailways.js
const riga = tornToZ.features.map(f => f.properties.railways);

let rigaForValga = [];
let rigaStations = [];

// Iegūst visus Rīgas apgabala dzelzceļa posmus, stacijas nosaukumus skatoties pēc kādi posmi ir aizliegti  
for (let i = 0; i < riga.length; i++) {
    if (riga[i] != "Torņkalns - Rīga" && riga[i] != "Savieno - Torņkalnu") {

        if (riga[i].includes(" - ")) {
            const firstStation = riga[i].split(" - ")[0];
            const secondStation = riga[i].split(" - ")[1];
            rigaStations.push(firstStation);
            rigaStations.push(secondStation);
        } else {
            rigaStations.push(riga[i]);
        }

        rigaForValga.push(riga[i]);
    }
}

// Iegūst visas dzelzceļa nosaukumus no CiekurkalnsValgaRailways.js 
let valga = cToV.features.map(f => f.properties.railways);

let removed = "";

// Pārmaina elementa Savieno Čiekurkalnu vietu masīvā
removed = valga.splice(valga.length - 1, 1);
valga.splice(0, 0, removed[0]);

let tempValgaStations = []

// Iegūs visas Čiekurkalns - Valga stacijas nosaukumus 
for (let i = 0; i < valga.length; i++) {
    if (i == valga.length - 1) {
        const firstStation = valga[i].split(" - ")[0];
        const secondStation = valga[i].split(" - ")[1];
        tempValgaStations.push(firstStation);
        tempValgaStations.push(secondStation);
    } else {
        const station = valga[i].split(" - ")[0];
        tempValgaStations.push(station);
    }
}

export let valgaStations = []

// Apvieno visas stacijas kopā
rigaStations.forEach(riga => {
    valgaStations.push(riga);
})

tempValgaStations.forEach(temp => {
    valgaStations.push(temp);
})

export let valgaRoutes = [];

// Apvieno visas dzelzceļa posmus kopā
rigaForValga.forEach(r => {
    valgaRoutes.push(r);
})

valga.forEach(v => {
    valgaRoutes.push(v);
})