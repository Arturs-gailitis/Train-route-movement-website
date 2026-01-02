const button = document.getElementById("atvērtMeklšanuPoga");
const box = document.getElementById("maršrutuMeklēšanasSadaļa");
const boxButton = document.getElementById("atcelt");
const footer = document.querySelector("footer");

// Ielādē footer 150 pikseļus no maršruta tabulas
document.addEventListener("DOMContentLoaded", () => {
    footer.style.position = "relative";
    footer.style.marginTop = "150px";
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
});