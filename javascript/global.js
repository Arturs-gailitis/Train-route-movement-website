if (window.location.href.includes("/eng/") || window.location.href.includes("runAll.php") || window.location.href.includes("users.php") || window.location.href.includes("messages.php") || window.location.href.includes("notifications.php")) {
    const navigationButtonLocations = document.querySelectorAll(".nav-item");
    let notification = null;

    // izlabo ziņojuma navigācijas pogu novietojumu anglu versijā
    if (navigationButtonLocations.length == 6) {
        notification = navigationButtonLocations[2];
    } else {
        notification = navigationButtonLocations[1];
    }

    notification.style.marginRight = "20px";
}

const profilButton = document.getElementById("lietotajs");
const profilArea = document.getElementById("profilaLaukums");
const opcijas = document.getElementById("opcijuLaukums");
const opcijuPoga = document.getElementById("opcijas");
const optionLogo = document.getElementById("opcijas");
let profilStatuss = false;

// parāda vai aizver profila sadaļu
profilButton.addEventListener("click", () => {
    if (profilStatuss == false) {
        profilArea.style.height = "120px";
        profilArea.style.display = "block";

        opcijas.style.height = "0px";
        opcijas.style.display = "none";

        optionLogo.style.transform = "rotate(-180deg)";

        profilStatuss = true;
    } else {
        profilArea.style.height = "0px";
        profilArea.style.display = "none";
        profilStatuss = false;
    }
})

// sakārto profila pogu dizainu angļu versijā
if (window.location.href.includes("/eng/") || window.location.href.includes("runAll.php") || window.location.href.includes("users.php") || window.location.href.includes("messages.php") || window.location.href.includes("notifications.php")) {
    const profilButtons = document.querySelectorAll(".profilaStatuss");
    if (profilButtons.length == 2) {
        let loginButton = profilButtons[0];
        loginButton.style.position = "relative";
        loginButton.style.paddingLeft = "45px";
        loginButton.style.paddingRight = "45px";
    } else {
        let logoutButton = profilButtons[0];
        logoutButton.style.position = "relative";
        logoutButton.style.paddingLeft = "20px";
        logoutButton.style.paddingRight = "20px";
    }
}

const profilLinks = document.querySelectorAll(".profilaStatuss");

// ja kursoru pieliec pie linkiem, tie nomaina krāsu
profilLinks.forEach(link => {
    link.addEventListener("mouseover", function() {
        link.style.color = "#0d6efd";
    })

    link.addEventListener("mouseout", function() {
        link.style.color = "white";
    })
});

let statuss = false;

// Nomaina opciju sadaļas augstumu nospiežzot pogu un pagriež logo
opcijuPoga.addEventListener("click", () => {
    if (statuss == false) {
        opcijas.style.height = "145px";
        opcijas.style.display = "block";

        profilArea.style.height = "0px";
        profilArea.style.display = "none";

        optionLogo.style.transform = "rotate(180deg)";
        
        statuss = true;
    } else {
        opcijas.style.height = "0px";
        opcijas.style.display = "none";
        optionLogo.style.transform = "rotate(-180deg)";
        statuss = false;
    }
});


const changeLanguage = document.getElementById("valoda");

// nomaina failu pēc atbisltošās valodas
changeLanguage.addEventListener("change", function() {
    if (this.value) {
        window.location.href = this.value;
    }
})

// iegūst šī brīža datums
const date = new Date();
const day = date.getDate();
const month = date.getMonth();
const year = date.getFullYear();

// saraksts ar mēnešiem latviski un angliski
const monthsLv = ["Janvārī", "Februārī", "Martā", "Aprīlī", "Maijā", "Jūnijā", "Jūlijā", "Augustā", "Septembrī", "Oktobrī", 
    "Novembrī", "Decembrī"
];

const monthsEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", 
    "November", "December"
];

// iegūst mēneša nosaukumu latviski un angliski
const monthNameLv = monthsLv[month]; 
const monthNameEng = monthsEng[month];

// parāda kad tiek iegūts no atvērtajiem datiem informācija un kad tika izstrādāts šis darbs
document.addEventListener("DOMContentLoaded", () => {
    const loadDate = document.getElementById("ielādesDatums");
    const projectDate = document.getElementById("projektaGads");

    // respektīvi ieliek footerī atbisltošo tekstu skatoties pēc valodas
    if (window.location.href.includes("/lv/") || window.location.href.includes("izpildaVisu.php") || window.location.href.includes("lietotajs.php") || window.location.href.includes("zinas.php") || window.location.href.includes("zinojumi.php")) {
        loadDate.textContent = year + ". gada " + day + ". " + monthNameLv;
    } else {
        loadDate.textContent = monthNameEng + " " + day + ", " + year;
    }

    projectDate.textContent = year;
})

const body = document.body;
const logo = document.getElementById("logo");
const themeChanger = document.getElementById("fonaIzmaiņasPoga");
const themeIcon = document.getElementById("themeIkona");
const ButtonAreaIcon = document.getElementById("atvērtPoguSadaļasIkona");
const cancelIcon = document.getElementById("atceltIcona");
const filterButtonIcon = document.getElementById("atvertFiltresanuIkona");
const searchAreaButton = document.getElementById("atvērtmeklēšanuIkona");
const reverseIcon = document.getElementById("apgireztIcona");
const infoIcons = document.querySelectorAll(".ikona");
const errorIcon = document.querySelectorAll(".kluda");
const buy = document.querySelectorAll(".buy");
const goLogo = document.querySelectorAll(".info");


// pārslēdz no tumšā režīma uz gaišo un atpakaļ
themeChanger.addEventListener("click", () => {
    body.classList.toggle("light-mode");
    localStorage.setItem("lightMode", body.classList.contains("light-mode"));

    if (body.classList.contains("light-mode")) {
        themeIcon.src = "http://localhost:8000/icons/lightTheme.svg";
        logo.src = "http://localhost:8000/icons/website icons/websiteIconDark.svg";
        
        if (cancelIcon !== null) {
            cancelIcon.src = "http://localhost:8000/icons/crossDark.svg";
        }

        if (ButtonAreaIcon !== null) {
            if (ButtonAreaIcon.src == "http://localhost:8000/icons/arrow-up.svg") {
                ButtonAreaIcon.src = "http://localhost:8000/icons/arrow-upDark.svg";
            } else if (ButtonAreaIcon.src == "http://localhost:8000/icons/arrow-down.svg") {
                ButtonAreaIcon.src = "http://localhost:8000/icons/arrow-downDark.svg";
            }
        }

        if (filterButtonIcon !== null) {
            if (filterButtonIcon.src == "http://localhost:8000/icons/arrow-up.svg") {
                filterButtonIcon.src = "http://localhost:8000/icons/arrow-upDark.svg";
            } else if (filterButtonIcon.src == "http://localhost:8000/icons/arrow-down.svg") {
                filterButtonIcon.src = "http://localhost:8000/icons/arrow-downDark.svg";
            }
        }

        if (searchAreaButton !== null) {
            searchAreaButton.src = "http://localhost:8000/icons/arrow-downDark.svg";
        }

        if (reverseIcon !== null) {
            reverseIcon.src = "http://localhost:8000/icons/reverseDark.svg";
        }

        if (infoIcons.length !== 0) {
            for (let i = 0; i < infoIcons.length; i++) {
                if (i == 0 || i == 1) {
                    infoIcons[i].src = "http://localhost:8000/icons/train-stationDark.svg";
                } else {
                    infoIcons[i].src = "http://localhost:8000/icons/dateDark.svg";
                }
            }
        }

        if (errorIcon.length !== 0) {
            for (let i = 0; i < errorIcon.length; i++) {
                errorIcon[i].src = "http://localhost:8000/icons/infoDark.svg";
            }
        }

        if (buy.length !== 0) {
            for (let i = 0; i < buy.length; i++) {
                buy[i].src = "http://localhost:8000/icons/buyDark.svg";
            }
        }

        if (goLogo.length !== 0) {
            for (let i = 0; i < goLogo.length; i++) {
                goLogo[i].src = "http://localhost:8000/icons/infoDark.svg";
            }
        }

    } else {
        themeIcon.src = "http://localhost:8000/icons/darkTheme.svg";
        logo.src = "http://localhost:8000/icons/website icons/websiteIconLight.svg";
        
        if (cancelIcon !== null) {
            cancelIcon.src = "http://localhost:8000/icons/cross.svg";
        }

        if (ButtonAreaIcon !== null) {
            if (ButtonAreaIcon.src == "http://localhost:8000/icons/arrow-upDark.svg") {
                ButtonAreaIcon.src = "http://localhost:8000/icons/arrow-up.svg";
            } else if (ButtonAreaIcon.src == "http://localhost:8000/icons/arrow-downDark.svg") {
                ButtonAreaIcon.src = "http://localhost:8000/icons/arrow-down.svg";
            }
        }

        if (filterButtonIcon !== null) {
            if (filterButtonIcon.src == "http://localhost:8000/icons/arrow-upDark.svg") {
                filterButtonIcon.src = "http://localhost:8000/icons/arrow-up.svg";
            } else if (filterButtonIcon.src == "http://localhost:8000/icons/arrow-downDark.svg") {
                filterButtonIcon.src = "http://localhost:8000/icons/arrow-down.svg";
            }
        }

        if (searchAreaButton !== null) {
            searchAreaButton.src = "http://localhost:8000/icons/arrow-down.svg";
        }

        if (reverseIcon !== null) {
            reverseIcon.src = "http://localhost:8000/icons/reverse.svg";
        }

        if (infoIcons.length !== 0) {
            for (let i = 0; i < infoIcons.length; i++) {
                if (i == 0 || i == 1) {
                    infoIcons[i].src = "http://localhost:8000/icons/train-station.svg";
                } else {
                    infoIcons[i].src = "http://localhost:8000/icons/date.svg";
                }
            }
        }

        if (errorIcon.length !== 0) {
            for (let i = 0; i < errorIcon.length; i++) {
                errorIcon[i].src = "http://localhost:8000/icons/info.svg";
            }
        }

        if (buy.length !== 0) {
            for (let i = 0; i < buy.length; i++) {
                buy[i].src = "http://localhost:8000/icons/buy.svg";
            }
        }

        if (goLogo.length !== 0) {
            for (let i = 0; i < goLogo.length; i++) {
                goLogo[i].src = "http://localhost:8000/icons/info.svg";
            }
        }
    }
})

// saglabā gaišo režīmu
if (localStorage.getItem("lightMode") === "true") {
    body.classList.add("light-mode");
    themeIcon.src = "http://localhost:8000/icons/lightTheme.svg";
    logo.src = "http://localhost:8000/icons/website icons/websiteIconDark.svg";

    if (cancelIcon !== null) {
        cancelIcon.src = "http://localhost:8000/icons/crossDark.svg";
    }    

    if (ButtonAreaIcon != null) {
        ButtonAreaIcon.src = "http://localhost:8000/icons/arrow-downDark.svg";
    }

    if (filterButtonIcon != null) {
        filterButtonIcon.src = "http://localhost:8000/icons/arrow-downDark.svg";
    }

    if (searchAreaButton != null) {
        searchAreaButton.src = "http://localhost:8000/icons/arrow-downDark.svg";
    }

    if (infoIcons.length !== 0) {
        for (let i = 0; i < infoIcons.length; i++) {
            if (i == 0 || i == 1) {
                infoIcons[i].src = "http://localhost:8000/icons/train-stationDark.svg";
            } else {
                infoIcons[i].src = "http://localhost:8000/icons/dateDark.svg";
            }
        }
    }

    if (errorIcon.length !== 0) {
        for (let i = 0; i < errorIcon.length; i++) {
            errorIcon[i].src = "http://localhost:8000/icons/infoDark.svg";
        }
    }

    if (reverseIcon !== null) {
        reverseIcon.src = "http://localhost:8000/icons/reverseDark.svg";
    }

    if (buy.length !== 0) {
        for (let i = 0; i < buy.length; i++) {
            buy[i].src = "http://localhost:8000/icons/buyDark.svg";
        }
    }

    if (goLogo.length !== 0) {
        for (let i = 0; i < goLogo.length; i++) {
            goLogo[i].src = "http://localhost:8000/icons/infoDark.svg";
        }
    }
} else {
    themeIcon.src = "http://localhost:8000/icons/darkTheme.svg";
    logo.src = "http://localhost:8000/icons/website icons/websiteIconLight.svg";

    if (cancelIcon !== null) {
        cancelIcon.src = "http://localhost:8000/icons/cross.svg";
    }

    if (ButtonAreaIcon != null) {
        ButtonAreaIcon.src = "http://localhost:8000/icons/arrow-down.svg";
    }

    if (filterButtonIcon != null) {
        filterButtonIcon.src = "http://localhost:8000/icons/arrow-down.svg";
    }

    if (searchAreaButton != null) {
        searchAreaButton.src = "http://localhost:8000/icons/arrow-down.svg";
    }

    if (errorIcon.length !== 0) {
        for (let i = 0; i < errorIcon.length; i++) {
            errorIcon[i].src = "http://localhost:8000/icons/info.svg";
        }
    }

    if (reverseIcon !== null) {
        reverseIcon.src = "http://localhost:8000/icons/reverse.svg";
    }

    if (buy.length !== 0) {
        for (let i = 0; i < buy.length; i++) {
            buy[i].src = "http://localhost:8000/icons/buy.svg";
        }
    }

    if (goLogo.length !== 0) {
        for (let i = 0; i < goLogo.length; i++) {
            goLogo[i].src = "http://localhost:8000/icons/info.svg";
        }
    }
}