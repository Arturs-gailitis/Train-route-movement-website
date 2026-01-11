const messsageBox = document.getElementById("zina");
const errorArea = document.getElementById("kluda");
const submitButton = document.getElementById("sutitZinu");

// Skatās vai šie elementi eksistē
if (messsageBox != null && errorArea != null && submitButton != null) {

    // skatās vai lietotāja ziņa ir no 10 līdz 250 rakstzīmēm, ja nē tad kļudās ziņojumu uzrāda un bloķē pogu
    messsageBox.addEventListener("input", () => {
        const message = messsageBox.value;

        if (message.length > 250) {
            const error = "Ziņa ir pārāk liela.";
            errorArea.innerText = error;
            submitButton.disabled = true;
        } else if (message.length < 10) {
            const error = "Ziņa ir pārāk maza."
            errorArea.innerText = error;
            submitButton.disabled = true;
        } else {
            errorArea.innerText = "";
            submitButton.disabled = false;
        }
    })
}

const goBackButton = document.getElementById("atgriezties");

// nospiežot pogu aizsūta uz sakumlapa.php
goBackButton.addEventListener("click", () => {
    window.location.href = "sakumlapa.php";
})