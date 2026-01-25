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
let rigaForSkulte = [];
let rigaForTukums = [];
let rigaStationsForVidzeme = [];
let rigaStationsForKurzeme = [];

// Iegūst visus Rīgas apgabala dzelzceļa posmus, stacijas nosaukumus skatoties pēc kādi posmi ir aizliegti  
for (let i = 0; i < riga.length; i++) {
    if (riga[i] != "Rīga - Torņakalns" && riga[i] != "Savieno Torņakalnu") {

        if (riga[i].includes(" - ")) {
            const firstStation = riga[i].split(" - ")[0];
            const secondStation = riga[i].split(" - ")[1];
            rigaStationsForVidzeme.push(firstStation);
            rigaStationsForVidzeme.push(secondStation);
        } else {
            rigaStationsForVidzeme.push(riga[i]);
        }

        rigaForValga.push(riga[i]);
        rigaForSkulte.push(riga[i]);
    
    }
    
    if (riga[i] != "Savieno Zemitānus" && riga[i] != "Rīga - Zemitāni") {
        
        if (riga[i].includes(" - ")) {
            const firstStation = riga[i].split(" - ")[0];
            const secondStation = riga[i].split(" - ")[1];
            rigaStationsForKurzeme.push(firstStation);
            rigaStationsForKurzeme.push(secondStation);
        } else {
            rigaStationsForKurzeme.push(riga[i]);
        }

        rigaForTukums.push(riga[i]);
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

// Iegūst visas dzelzceļa nosaukumus no BrasaSkulteRailways.geoJson.js un elementus sagriež otrādi 
let skulte = bToS.features.map(f => f.properties.railways);
skulte.reverse();

let tempSkulteStations = []

// Iegūs visas Brasa - Skulte stacijas nosaukumus
for (let i = 0; i < skulte.length; i++) {
    if (i == skulte.length - 1) {
        const firstStation = skulte[i].split(" - ")[0];
        const secondStation = skulte[i].split(" - ")[1];
        tempSkulteStations.push(firstStation);
        tempSkulteStations.push(secondStation);
    } else {
        const station = skulte[i].split(" - ")[0];
        tempSkulteStations.push(station);
    }
}

// Iegūst visas dzelzceļa nosaukumus no TukumsZasulauksRailways.geoJson.js un elementus sagriež otrādi
let tukums = tToZ.features.map(f => f.properties.railways);
tukums.reverse();

let tempTukumsStations = [];

// Iegūs visas Tukums II - Zasulauks stacijas nosaukumus
for (let i = 0; i < tukums.length; i++) {
    if (i == tukums.length - 1) {
        const firstStation = tukums[i].split(" - ")[0];
        const secondStation = tukums[i].split(" - ")[1];
        tempTukumsStations.push(firstStation);
        tempTukumsStations.push(secondStation);
    } else {
        const station = tukums[i].split(" - ")[0];
        tempTukumsStations.push(station);
    }
}

export let valgaStations = [];
export let skulteStations = [];
export let tukumsStations = [];

// Apvieno visas stacijas kopā
rigaStationsForVidzeme.forEach(riga => {
    valgaStations.push(riga);
    skulteStations.push(riga);
})

tempValgaStations.forEach(temp => {
    valgaStations.push(temp);
})

tempSkulteStations.forEach(temp => {
    skulteStations.push(temp);
})

rigaStationsForKurzeme.forEach(riga => {
    tukumsStations.push(riga);
})

tempTukumsStations.forEach(temp => {
    tukumsStations.push(temp);
})

export let valgaRoutes = [];
export let skulteRoutes = [];
export let tukumsRoutes = [];

// Apvieno visas dzelzceļa posmus kopā
rigaForValga.forEach(r => {
    valgaRoutes.push(r);
})

valga.forEach(v => {
    valgaRoutes.push(v);
})

rigaForSkulte.forEach(r => {
    skulteRoutes.push(r);
})

skulte.forEach(s => {
    skulteRoutes.push(s);
})

rigaForTukums.forEach(r => {
    tukumsRoutes.push(r);
})

tukums.forEach(t => {
    tukumsRoutes.push(t);
})