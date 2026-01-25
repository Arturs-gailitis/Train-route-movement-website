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
let rigaForLiepaja = [];
let rigaForLatgale = [];
let rigaStationsForVidzeme = [];
let rigaStationsForKurzeme = [];
let rigaStationForLatgale = [];

// Iegūst visus Rīgas apgabala dzelzceļa posmus, stacijas nosaukumus skatoties pēc kādi posmi ir aizliegti  
for (let i = 0; i < riga.length; i++) {
    if (riga[i] != "Rīga - Torņakalns" && riga[i] != "Savieno Torņakalnu" && riga[i] != "Rīga - Vagonu parks") {

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
    
    if (riga[i] != "Savieno Zemitānus" && riga[i] != "Rīga - Zemitāni" && riga[i] != "Rīga - Vagonu parks") {
        
        if (riga[i].includes(" - ")) {
            const firstStation = riga[i].split(" - ")[0];
            const secondStation = riga[i].split(" - ")[1];
            rigaStationsForKurzeme.push(firstStation);
            rigaStationsForKurzeme.push(secondStation);
        } else {
            rigaStationsForKurzeme.push(riga[i]);
        }

        rigaForTukums.push(riga[i]);
        rigaForLiepaja.push(riga[i]);
    }

    if (riga[i] == "Rīga - Vagonu parks") {
        const station = riga[i].split(" - ")[0];
        rigaStationForLatgale.push(station);
        rigaForLatgale.push(riga[i]);
    }
}

let removed = "";

// Iegūst visas dzelzceļa posmu nosaukumus no CiekurkalnsValgaRailways.js 
let valga = cToV.features.map(f => f.properties.railways);

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

// Iegūst visas dzelzceļa posmu nosaukumus no BrasaSkulteRailways.geoJson.js un elementus sagriež otrādi 
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

// Iegūst visas dzelzceļa posmu nosaukumus no TukumsZasulauksRailways.geoJson.js un elementus sagriež otrādi
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

// Iegūst visas dzelzceļa posmu nosaukumus no LiepājaSkrundeRailways.js, SkrundeDobeleRailways.js, DobeleBieriniRailways.js
let liepaja = lToS.features.map(f => f.properties.railways);
const s = sToD.features.map(f => f.properties.railways);
const d = dToB.features.map(f => f.properties.railways);

// saliek visus dzelzceļa nosaukumus kopā
s.forEach(sk => {
    liepaja.push(sk);
})

d.forEach(dob => {
    liepaja.push(dob);
})

// dzelzceļa nosaukumus masīvā apgriež otrādāk
liepaja.reverse();

let tempLiepajaStations = [];

// Iegūs visas Liepāja - Bieriņi stacijas nosaukumus
for (let i = 0; i < liepaja.length; i++) {
    if (i == liepaja.length - 1) {
        const firstStation = liepaja[i].split(" - ")[0];
        const secondStation = liepaja[i].split(" - ")[1];
        tempLiepajaStations.push(firstStation);
        tempLiepajaStations.push(secondStation);
    } else {
        const station = liepaja[i].split(" - ")[0];
        tempLiepajaStations.push(station);
    }
}

let gulbene = [];
let zilupe = [];
let indra = [];

// Iegūst visas dzelzceļa posmu nosaukumus no VagonuparksPlavinasRailways.js
let plavinas = vToP.features.map(f => f.properties.railways);

// Pārmaina elementa Savieno Vagona parku vietu masīvā
removed = plavinas.splice(plavinas.length - 1, 1);
plavinas.splice(0, 0, removed[0]);

// Iegūst visas dzelzceļa posmu nosaukumus no PlavinasGulbeneRailways.js, KrustpilsZilupeRailways.js
const g = pToG.features.map(f => f.properties.railways);

// Savieno plaviņu dzelzceļa posmu gulbenes, zilupes, indras masīvos
plavinas.forEach(p => {
    gulbene.push(p);
    zilupe.push(p);
    indra.push(p);
})

// savieno gulbenes maršruta posmus masīvā
g.forEach(gr => {
    gulbene.push(gr);
})

let tempGulbeneStations = []

// Iegūs visas Rīga - Gulbene stacijas nosaukumus
for (let i = 0; i < gulbene.length; i++) {
    if (i == gulbene.length - 1) {
        const firstStation = gulbene[i].split(" - ")[0];
        const secondStation = gulbene[i].split(" - ")[1];
        tempGulbeneStations.push(firstStation);
        tempGulbeneStations.push(secondStation);
    } else {
        const station = gulbene[i].split(" - ")[0];
        tempGulbeneStations.push(station);
    }
}

// Iegūst visas dzelzceļa posmu nosaukumus no PlavinasIndraRailwats.js
const i = pToI.features.map(f => f.properties.railways);

// savieno ar plaviņu masīvu un izņem lieko Daugavpils - Aglona maršruta posmu
i.forEach(ind => {
    if (ind != 'Daugavpils - Aglona') {
        indra.push(ind);
    }
})

let tempIndraStations = [];

// Iegūs visas Rīga - Indra stacijas nosaukumus
for (let i = 0; i < indra.length; i++) {
    if (i == indra.length - 1) {
        const firstStation = indra[i].split(" - ")[0];
        const secondStation = indra[i].split(" - ")[1];
        tempIndraStations.push(firstStation);
        tempIndraStations.push(secondStation);
    } else {
        const station = indra[i].split(" - ")[0];
        tempIndraStations.push(station);
    }
}

// Iegūst visas dzelzceļa posmu nosaukumus no KrustpilsZilupeRailways.js
const z = kToZ.features.map(f => f.properties.railways);

// Iegūst konkrētus dzelzceļa posmus no indras masīva
zilupe.push(i[0]);
zilupe.push(i[1]);

// savieno zilupes maršruta posmus masīvā
z.forEach(zil => {
    zilupe.push(zil);
})

let tempZilupeStations = [];

// Iegūst visas Rīga - Zilupe stacijas nosaukumus
for (let i = 0; i < zilupe.length; i++) {
    if (i == zilupe.length - 1) {
        const firstStation = zilupe[i].split(" - ")[0];
        const secondStation = zilupe[i].split(" - ")[1];
        tempZilupeStations.push(firstStation);
        tempZilupeStations.push(secondStation);
    } else {
        const station = zilupe[i].split(" - ")[0];
        tempZilupeStations.push(station);
    }
}

export let valgaStations = [];
export let skulteStations = [];
export let tukumsStations = [];
export let liepajaStations = [];
export let gulbeneStations = [];
export let indraStations = [];
export let zilupeStations = [];

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
    liepajaStations.push(riga);
})

tempTukumsStations.forEach(temp => {
    tukumsStations.push(temp);
})

tempLiepajaStations.forEach(temp => {
    liepajaStations.push(temp);
})

rigaStationForLatgale.forEach(temp => {
    gulbeneStations.push(temp);
    indraStations.push(temp);
    zilupeStations.push(temp);
})

tempGulbeneStations.forEach(temp => {
    gulbeneStations.push(temp);
})

tempIndraStations.forEach(temp => {
    indraStations.push(temp);
})

tempZilupeStations.forEach(temp => {
    zilupeStations.push(temp);
})

export let valgaRoutes = [];
export let skulteRoutes = [];
export let tukumsRoutes = [];
export let liepajaRoutes = [];
export let gulbeneRoute = [];
export let indraRoute = [];
export let zilupeRoute = [];

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

rigaForLiepaja.forEach(r => {
    liepajaRoutes.push(r);
})

liepaja.forEach(l => {
    liepajaRoutes.push(l);
})

rigaForLatgale.forEach(r => {
    gulbeneRoute.push(r);
    indraRoute.push(r);
    zilupeRoute.push(r);
})

gulbene.forEach(g => {
    gulbeneRoute.push(g);
})

indra.forEach(i => {
    indraRoute.push(i);
})

zilupe.forEach(z => {
    zilupeRoute.push(z);
})