const errorMessage = document.getElementById("errors").innerText;
const registerArea = document.getElementById("registresanas");
const submitButton = document.getElementById("registracijasPoga");

// Izmaina reģistrācijas laukumu, ja notiek kāda kļūda
submitButton.addEventListener("click", () => {
    if (errorMessage.length != 0) {
        registerArea.style.height = "500px";
    } else {
        registerArea.style.height = "auto";
    }
})