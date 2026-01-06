const errorMessage = document.getElementById("errors").innerText;
const LoginArea = document.getElementById("pieteiksanas");
const submitButton = document.getElementById("pieteiksanasPoga");

// Izmaina pietiekšanās laukumu, ja notiek kāda kļūda
submitButton.addEventListener("click", () => {
    if (errorMessage.length != 0) {
        LoginArea.style.height = "400px";
    } else {
        LoginArea.style.height = "auto";
    }
})