const opcijas = document.getElementById("opcijuLaukums");
const opcijuPoga = document.getElementById("opcijas");
let statuss = false;

// Nomaina opciju sadaļas augstumu nospiežzot pogu
opcijuPoga.addEventListener("click", () => {
    if (statuss == false) {
        opcijas.style.height = "145px";
        opcijas.style.display = "block";
        statuss = true;
    } else {
        opcijas.style.height = "0px";
        opcijas.style.display = "none";
        statuss = false;
    }
});

const optionLogo = document.getElementById("opcijas");

// Kad uzpiež opciju pogu navigācijā, tad tas logo griežās
opcijuPoga.addEventListener("click", () => {
    if (statuss == false) {
        optionLogo.style.transform = "rotate(180deg)";
    } else {
        optionLogo.style.transform = "rotate(-180deg)";
    }
})


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
