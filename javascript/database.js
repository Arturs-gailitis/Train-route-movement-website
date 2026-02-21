const openButtons = document.getElementById("atvertPoguSadaļasPoga");
const ButtonsArea = document.getElementById("poguLaukums");
const buttonIcon = document.getElementById("atvērtPoguSadaļasIkona");
let buttonStatuss = false

// Parādīt un aizvērt pogu sadaļu
openButtons.addEventListener("click", () => {
    if (buttonStatuss == false) {
        ButtonsArea.style.display = "block";
        buttonIcon.src = "http://localhost:8000/icons/arrow-up.svg";
        buttonStatuss = true;
        if (window.location.href.includes("/lv/")) {
            openButtons.title = "Aizvērt pogas sadaļu";
        } else {
            openButtons.title = "Close button section";
        }
    } else {
        ButtonsArea.style.display = "none";
        buttonIcon.src = "http://localhost:8000/icons/arrow-down.svg";
        buttonStatuss = false;
        if (window.location.href.includes("/lv/")) {
            openButtons.title = "Parādīt pogas sadaļu";
        } else {
            openButtons.title = "Show button section";
        }
    }
})

const resetTrains = document.getElementById("vilcieni");
const resetUsers = document.getElementById("lietotaja");
const resetMessages = document.getElementById("kontakti");
const resetNotifications = document.getElementById("pazinojumi");

// ieliek vilciena informācijas skripta adresi
resetTrains.addEventListener("click", () => {
    window.location.href = "http://localhost:8000/setup/runAll.php";
})

// ieliek lietotāja skripta adresi
resetUsers.addEventListener("click", () => {
    window.location.href = "http://localhost:8000/setup/users.php";
})

// ieliek lietotāja ziņojuma skripta adresi
resetMessages.addEventListener("click", () => {
    window.location.href = "http://localhost:8000/setup/messages.php";
})

// ieliek paziņojuma skripta adresi
resetNotifications.addEventListener("click", () => {
    window.location.href = "http://localhost:8000/setup/notifications.php";
})

const calendarTable = document.querySelector(".kalendaraTabula");
const routeTable = document.querySelector(".MarsrutaTabula");
const stationTable = document.querySelector(".StacijasTabula");
const stopsTable = document.querySelector(".BraucienuApstasanasTabula");
const tripsTable = document.querySelector(".BraucienuTabula");
const usersTable = document.querySelector(".LietotajuTabula");
const messageTable = document.querySelector(".ZinuTabula");
const notificationTable = document.querySelector(".PazinojumuTabula");

const calendarButton = document.getElementById("kalendars");
const routeButton = document.getElementById("marsruts");
const stationButton = document.getElementById("stacija");
const stopsButton = document.getElementById("apstasanas");
const tripsButton = document.getElementById("braucieni");
const usersButton = document.getElementById("lietotaji");
const messageButton = document.getElementById("zinojumi");
const notificationButton = document.getElementById("paz");

const newRecord = document.getElementById("izveidot");

// kad lapa ielādējās parāda tikai kalendāra tabulu
document.addEventListener("DOMContentLoaded", () => {
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
    messageTable.style.display = "none";
    notificationTable.style.display = "none";
    newRecord.href = "izveidot.php?tabula=calendar";
})

// kad nospiež pogu parāda tikai kalendāra tabulu
calendarButton.addEventListener("click", () => {
    calendarTable.style.display = "block";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
    messageTable.style.display = "none";
    notificationTable.style.display = "none";
    newRecord.href = "izveidot.php?tabula=calendar";
    newRecord.style.pointerEvents = "auto";
    newRecord.style.backgroundColor = "#1f7a1f";
})

// kad nospiež pogu parāda tikai maršruta tabulu
routeButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "block";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
    messageTable.style.display = "none";
    notificationTable.style.display = "none";
    newRecord.href = "izveidot.php?tabula=routes";
    newRecord.style.pointerEvents = "auto";
    newRecord.style.backgroundColor = "#1f7a1f";
})

// kad nospiež pogu parāda tikai stacijas tabulu
stationButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "block";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
    messageTable.style.display = "none";
    notificationTable.style.display = "none";
    newRecord.href = "izveidot.php?tabula=stops";
    newRecord.style.pointerEvents = "auto";
    newRecord.style.backgroundColor = "#1f7a1f";
})

// kad nospiež pogu parāda tikai brauciena apstāšanās tabulu
stopsButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "block";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
    messageTable.style.display = "none";
    notificationTable.style.display = "none";
    newRecord.href = "izveidot.php?tabula=stop_times";
    newRecord.style.pointerEvents = "auto";
    newRecord.style.backgroundColor = "#1f7a1f";
})

// kad nospiež pogu parāda tikai brauciena tabulu
tripsButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "block";
    usersTable.style.display = "none";
    messageTable.style.display = "none";
    notificationTable.style.display = "none";
    newRecord.href = "izveidot.php?tabula=trips";
    newRecord.style.pointerEvents = "auto";
    newRecord.style.backgroundColor = "#1f7a1f";
})

// kad nospiež pogu parāda tikai lietotāja tabulu
usersButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "block";
    messageTable.style.display = "none";
    notificationTable.style.display = "none";
    newRecord.style.pointerEvents = "none";
    newRecord.style.backgroundColor = "grey";
})

// kad nospiež pogu parāda tikai ziņojumu tabulu
messageButton.addEventListener("click", () => {
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
    notificationTable.style.display = "none";
    messageTable.style.display = "block";
    newRecord.style.pointerEvents = "none";
    newRecord.style.backgroundColor = "grey";
})

// kad nospiež pogu parāda tikai paziņojumu tabulu
notificationButton.addEventListener("click", () => {
    notificationTable.style.display = "block";
    calendarTable.style.display = "none";
    routeTable.style.display = "none";
    stationTable.style.display = "none";
    stopsTable.style.display = "none";
    tripsTable.style.display = "none";
    usersTable.style.display = "none";
    messageTable.style.display = "none";
    newRecord.href = "izveidot.php?tabula=notifications";
    newRecord.style.pointerEvents = "auto";
    newRecord.style.backgroundColor = "#1f7a1f";
})