const button = document.getElementById("meklet");
const box = document.getElementById("galvenaSekcija");
const meklesana = document.getElementById("maršrutuMeklēšana");
const iziet = document.getElementById("atcelt");

button.addEventListener("click", () => {

    const garums = box.offsetHeight;

    box.style.display = "none";
    meklesana.style.display = "block";
    meklesana.style.height = `${garums}px`;
    
});

iziet.addEventListener("click", () => {
    meklesana.style.display = "none";
    box.style.display = "block";
});