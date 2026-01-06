const calendarTable = document.querySelector(".kalendaraTabula");
const routeTable = document.querySelector(".MarsrutaTabula");
const stationTable = document.querySelector(".StacijasTabula");
const stopsTable = document.querySelector(".BraucienuApstasanasTabula");
const tripsTable = document.querySelector(".BraucienuTabula");
const usersTable = document.querySelector(".LietotajuTabula");

const calendarButton = document.getElementById("kalendars");
const routeButton = document.getElementById("marsruts");
const stationButton = document.getElementById("stacija");
const stopsButton = document.getElementById("apstasanas");
const tripsButton = document.getElementById("braucieni");
const usersButton = document.getElementById("lietotaji");

// kad lapa ielādējās parāda tikai kalendāra tabulu
document.addEventListener("DOMContentLoaded", () => {
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
})

// kad nospiež pogu parāda tikai kalendāra tabulu
calendarButton.addEventListener("click", () => {
    calendarTable.style.display = "block";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
})

// kad nospiež pogu parāda tikai maršruta tabulu
routeButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "block";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
})

// kad nospiež pogu parāda tikai stacijas tabulu
stationButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "block";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
})

// kad nospiež pogu parāda tikai brauciena apstāšanās tabulu
stopsButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "block";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
})

// kad nospiež pogu parāda tikai brauciena tabulu
tripsButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "block";
    usersTable.style.display = "none";
})

// kad nospiež pogu parāda tikai lietotāja tabulu
usersButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "block";
})