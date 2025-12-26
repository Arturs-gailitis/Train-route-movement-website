import { bToS } from "./BrasaSkulteRailways.geoJson.js";
import { tToZ } from "./TukumsZasulauksRailways.geoJson.js";
import { lToS } from "./LiepājaSkrundeRailways.js";
import { sToD } from "./SkrundeDobeleRailways.js";
import { dToB } from "./DobeleBieriniRailways.js";
import { vToP } from "./VagonuparksPlavinasRailways.js";
import { pToI } from "./PlavinasIndraRailwats.js";
import { kToZ } from "./KrustpilsZilupeRailways.js";

// apvieno visus dzelzceļa posmus vienā masīvā
export const railtrack = [bToS, tToZ, lToS, sToD, dToB, vToP, pToI, kToZ];