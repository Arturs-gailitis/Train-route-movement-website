const button = document.getElementById("meklet");
const box = document.getElementById("galvenaSekcija");
const meklesana = document.getElementById("maršrutuMeklēšana");

button.addEventListener("click", () => {
    box.style.display = "none";
    meklesana.style.display = "block";
});