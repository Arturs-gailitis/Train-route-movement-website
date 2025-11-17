const opcijas = document.getElementById("opcijuLaukums");
const opcijuPoga = document.getElementById("opcijas");
let statuss = false;

opcijuPoga.addEventListener("click", () => {
    if (statuss == false) {
        opcijas.style.height = "145px";
        statuss = true;
    } else {
        opcijas.style.height = "0px";
        statuss = false;
    }
});
