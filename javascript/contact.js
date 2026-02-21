const messsageBox = document.getElementById("zina");
const errorArea = document.getElementById("kluda");
const submitButton = document.getElementById("sutitZinu");

// Skatās vai šie elementi eksistē
if (messsageBox != null && errorArea != null && submitButton != null) {

    // skatās vai lietotāja ziņa ir no 10 līdz 250 rakstzīmēm, ja nē tad kļudās ziņojumu uzrāda un bloķē pogu
    messsageBox.addEventListener("input", () => {
        const message = messsageBox.value;
        let error = ""

        if (message.length > 250) {
            if (window.location.href.includes("/lv/")) {
                error = "Ziņa ir pārāk liela.";
            } else {
                error = "The message is too big.";
            }
            errorArea.innerText = error;
            submitButton.disabled = true;
        } else if (message.length < 10) {
            if (window.location.href.includes("/lv/")) {
                error = "Ziņa ir pārāk maza."
            } else {
                error = "The message is too small.";
            }
            errorArea.innerText = error;
            submitButton.disabled = true;
        } else {
            errorArea.innerText = "";
            submitButton.disabled = false;
        }
    })
}

const goBackButton = document.getElementById("atgriezties");

// nospiežot pogu aizsūta uz sakumlapu
if (goBackButton != null) {
    goBackButton.addEventListener("click", () => {
        if (window.location.href.includes("/lv/")) {
            window.location.href = "sakumlapa.php";
        } else {
            window.location.href = "main.php";
        }
    })
}