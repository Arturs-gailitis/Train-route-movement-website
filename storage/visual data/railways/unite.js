import { bToS } from "./BrasaSkulteRailways.geoJson.js";
import { tToZ } from "./TukumsZasulauksRailways.geoJson.js";
import { lToS } from "./LiepājaSkrundeRailways.js";

// apvieno visus dzelzceļa posmus vienā masīvā
export const railtrack = [bToS, tToZ, lToS];