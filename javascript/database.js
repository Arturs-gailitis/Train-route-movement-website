const openButtons = document.getElementById("atvertPoguSadaļasPoga");
const ButtonsArea = document.getElementById("poguLaukums");
const buttonIcon = document.getElementById("atvērtPoguSadaļasIkona");
let buttonStatuss = false

// Parādīt un aizvērt pogu sadaļu
openButtons.addEventListener("click", () => {
    if (buttonStatuss == false) {
        ButtonsArea.style.display = "block";

        if (localStorage.getItem("lightMode") == "true") {
            buttonIcon.src = "http://localhost:8000/icons/arrow-upDark.svg";
        } else {
            buttonIcon.src = "http://localhost:8000/icons/arrow-up.svg";
        }

        buttonStatuss = true;
        if (window.location.href.includes("/lv/")) {
            openButtons.title = "Aizvērt pogas sadaļu";
        } else {
            openButtons.title = "Close button section";
        }
    } else {
        ButtonsArea.style.display = "none";

        if (localStorage.getItem("lightMode") == "true") {
            buttonIcon.src = "http://localhost:8000/icons/arrow-downDark.svg";
        } else {
            buttonIcon.src = "http://localhost:8000/icons/arrow-down.svg";
        }
        
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