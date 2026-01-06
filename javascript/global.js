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

const themeChanger = document.getElementById("fonaIzmaiņasPoga");
const themeIcon = document.getElementById("themeIkona");

// nomaina theme ikonu ja lietotājs uz tās nospiež
themeChanger.addEventListener("click", () => {
    if (themeIcon.src == "http://localhost:8000/icons/lightTheme.svg") {
        themeIcon.src = "http://localhost:8000/icons/darkTheme.svg";
    } else {
        themeIcon.src = "http://localhost:8000/icons/lightTheme.svg";
    }
})

// iegūst šī brīža datums
const date = new Date();
const day = date.getDate();
const month = date.getMonth();
const year = date.getFullYear();

// saraksts ar mēnešiem
const months = ["Janvārī", "Februārī", "Martā", "Aprīlī", "Maijā", "Jūnijā", "Jūlijā", "Augustā", "Septembrī", "Oktobrī", 
    "Novembrī", "Decembrī"
];

// iegūst mēneša nosaukumu
const monthName = months[month]; 

// parāda kad tiek iegūts no atvērtajiem datiem informācija un kad tika izstrādāts šis darbs
document.addEventListener("DOMContentLoaded", () => {
    const loadDate = document.getElementById("ielādesDatums");
    const projectDate = document.getElementById("projektaGads");
    loadDate.textContent = year + ". gada " + day + ". " + monthName;
    projectDate.textContent = year;
})