document.addEventListener("DOMContentLoaded", () => {

    const routeNameValue = document.getElementById("marsrutaNosaukums");
    const routeName = document.getElementById("nosaukums");
    const routeNameLabel = document.getElementById("nosaukumaTeksts");

    // Kad novieto peli uz pirmās maršruta nosaukuma vērtības parādās tās nosaukums 
    routeNameValue.addEventListener("mouseover", () => {
        routeNameLabel.style.display = "flex";
        routeName.style.border = "3px solid black";
    });

    // Kad peli aiztum prom no pirmās maršruta nosaukuma vērtības pazūd tās nosaukums
    routeNameValue.addEventListener("mouseout", () => {
        routeNameLabel.style.display = "none";
        routeName.style.border = "none";
    });

    const startValue = document.getElementById("sakumaLaiks");
    const start = document.getElementById("sakums");
    const startLabel = document.getElementById("sakumaTeksts");

    // Kad novieto peli uz pirmās atiešanas laika vērtības parādās tās nosaukums 
    startValue.addEventListener("mouseover", () => {
        startLabel.style.display = "flex";
        start.style.border = "3px solid black";
    });

    // Kad peli aiztum prom no pirmās atiešanas laika vērtības pazūd tās nosaukums
    startValue.addEventListener("mouseout", () => {
        startLabel.style.display = "none";
        start.style.border = "none";
    });

    const endValue = document.getElementById("beigasLaiks");
    const end = document.getElementById("beigas");
    const endLabel = document.getElementById("beigasTeksts");

    // Kad novieto peli uz pirmās pienākšanas laika vērtības parādās tās nosaukums
    endValue.addEventListener("mouseover", () => {
        endLabel.style.display = "flex";
        end.style.border = "3px solid black";
    });

    // Kad peli aiztum prom no pirmās pienākšanas laika vērtības pazūd tās nosaukums
    endValue.addEventListener("mouseout", () => {
        endLabel.style.display = "none";
        end.style.border = "none";
    });

    const tripIdValue = document.getElementById("identifikators");
    const tripId = document.getElementById("marsrutaIdentifikators");
    const tripIdLabel = document.getElementById("identifikatorsTeksts");

    // Kad novieto peli uz pirmā maršruta id vērtības parādās tās nosaukums
    tripIdValue.addEventListener("mouseover", () => {
        tripIdLabel.style.display = "flex";
        tripId.style.border = "3px solid black";
    });

    // Kad peli aiztum prom no pirmā maršruta id vērtības pazūd tās nosaukums
    tripIdValue.addEventListener("mouseout", () => {
        tripIdLabel.style.display = "none";
        tripId.style.border = "none";
    });

    const tripTimeValue = document.getElementById("marsrutaLaiks");
    const tripTime = document.getElementById("laiks");
    const tripTimeLabel = document.getElementById("laikaTeksts");

    // Kad novieto peli uz pirmā kopējā ceļa laika vērtības parādās tās nosaukums
    tripTimeValue.addEventListener("mouseover", () => {
        tripTimeLabel.style.display = "flex";
        tripTime.style.border = "3px solid black";
    });

    // Kad peli aiztum prom no pirmā kopējā ceļa laika vērtības pazūd tās nosaukums
    tripTimeValue.addEventListener("mouseout", () => {
        tripTimeLabel.style.display = "none";
        tripTime.style.border = "none";
    });

    const priceValue = document.getElementById("pirktPoga");
    const price = document.getElementById("pirkt");
    const priceLabel = document.getElementById("pirktTeksts");

    // Kad novieto peli uz pirmās biļešu iegādes pogas parādās tās nosaukums
    priceValue.addEventListener("mouseover", () => {
        priceLabel.style.display = "flex";
        price.style.border = "3px solid black";
    });

    // Kad peli aiztum prom no pirmās biļešu iegādes pogas pazūd tās nosaukums
    priceValue.addEventListener("mouseout", () => {
        priceLabel.style.display = "none";
        price.style.border = "none";
    });

    const infoValue = document.getElementById("infoPoga");
    const info = document.getElementById("info");
    const infoLabel = document.getElementById("infoTeksts");

    // Kad novieto peli uz pirmās vairāk info pogas parādās tās nosaukums
    infoValue.addEventListener("mouseover", () => {
        infoLabel.style.display = "flex";
        info.style.border = "3px solid black";
    });

    // Kad peli aiztum prom no pirmās vairāk infopogas pazūd tās nosaukums
    infoValue.addEventListener("mouseout", () => {
        infoLabel.style.display = "none";
        info.style.border = "none";
    });
});