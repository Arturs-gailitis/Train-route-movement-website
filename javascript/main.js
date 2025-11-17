const button = document.getElementById("meklet");
const box = document.getElementById("galvenaSekcija");
const meklesana = document.getElementById("maršrutuMeklēšana");
const iziet = document.getElementById("atcelt");
const garums = box.offsetHeight;

button.addEventListener("click", () => {
    box.style.display = "none";
    meklesana.style.display = "flex";
    meklesana.style.height = `${garums}px`;
});

iziet.addEventListener("click", () => {
    meklesana.style.display = "none";
    box.style.display = "block";
});