import {stations} from "../storage/visual data/stations.js";

const button = document.getElementById("atvērtMeklšanuPoga");
const box = document.getElementById("maršrutuMeklēšanasSadaļa");
const boxButton = document.getElementById("atcelt");
const footer = document.querySelector("footer");
const error = document.querySelector(".kluduZinojums");
const records = document.querySelectorAll("tr");

// Ielādē footer 250 pikseļus no maršruta tabulas
document.addEventListener("DOMContentLoaded", () => {
    if (error != null) {
        footer.style.position = "fixed";
    } else if (records.length < 7) {
        footer.style.position = "fixed";
    } else {
        footer.style.position = "relative";
        footer.style.marginTop = "250px";
    }
})

// Parāda maršruta meklēšanas sadaļu
button.addEventListener("click", () => {
    box.style.display = "block";
    button.style.display = "none";
    footer.style.marginTop = "200px";
    footer.style.position = "relative";
});

// Aizver maršruta meklēšanas sadaļu
boxButton.addEventListener("click", () => {
    box.style.display = "none";
    button.style.display = "block";
    footer.style.marginTop = "-200px";

    if (error != null || records.length < 7) {
        footer.style.position = "fixed";
    }
});

// automātiski ieliek url linku uz 1188.lv
document.addEventListener("DOMContentLoaded", () => {
    const buyButtons = document.querySelectorAll(".pirktPogas");

    const firstStation = document.getElementById("sakumaStacija").innerText;
    const lastStation = document.getElementById("beiguStacija").innerText;
    const date = document.getElementById("datumaInfo").innerText;

    let firstStationName;
    let fisrtStationNumber;
    let lastStationName;
    let lastStationNumber;

    // iegūst nepieciešamo info priekš url
    stations.forEach(stat => {
        if (firstStation == stat.sname) {
            firstStationName = stat.alt;
            fisrtStationNumber = stat.code;
        } else if (lastStation == stat.sname) {
            lastStationName = stat.alt;
            lastStationNumber = stat.code;
        }
    })

    const url = "https://www.1188.lv/satiksme/vilcieni/" + firstStationName + "/" + lastStationName + "/" + fisrtStationNumber +"/" + lastStationNumber + "/diena/" + date;

    // ieliek href vērtību katrai pogai
    buyButtons.forEach(b => {
        b.href = url;
    })
})