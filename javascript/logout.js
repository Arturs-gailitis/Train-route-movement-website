const goButton = document.getElementById("ietUzSakumu");

// nospiežot pogu aizsūta uz sākumlapu
goButton.addEventListener("click", () => {
    if (window.location.href.includes("/lv/")) {
        window.location.href = "sakumlapa.php";
    } else {
        window.location.href = "main.php";
    }
})