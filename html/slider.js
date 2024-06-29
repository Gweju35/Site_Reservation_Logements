const rangeInput = document.querySelectorAll(".range-input input"),
      priceInput = document.querySelectorAll(".price-input input"),
      progress = document.querySelector(".slider .progress");

let priceGap = 20;



// Fonction pour mettre à jour les éléments de plage en fonction des valeurs des éléments de saisie de prix
function updateRangeInputs() {
    let minVal = parseInt(priceInput[0].value),
        maxVal = parseInt(priceInput[1].value);

    if ((maxVal - minVal >= priceGap) && maxVal <= 500) {
        rangeInput[0].value = minVal;
        rangeInput[1].value = maxVal;
        progress.style.left = (minVal / rangeInput[0].max) * 100 + "%";
        progress.style.width = ((maxVal - minVal) / (priceInput[1].max - priceInput[0].min)) * 100 + "%"; // Modifiez la largeur de la barre de progression en fonction de la plage de valeurs sélectionnée
    }
}

// Fonction pour mettre à jour les éléments de saisie de prix en fonction des valeurs des éléments de plage
function updatePriceInputs() {
    let minVal = parseInt(rangeInput[0].value),
        maxVal = parseInt(rangeInput[1].value);

    priceInput[0].value = minVal;
    priceInput[1].value = maxVal;
    progress.style.left = (minVal / rangeInput[0].max) * 100 + "%";
    progress.style.width = ((maxVal - minVal) / (priceInput[1].max - priceInput[0].min)) * 100 + "%"; // Modifiez la largeur de la barre de progression en fonction de la plage de valeurs sélectionnée

}

priceInput.forEach(input => {
    input.addEventListener("input", function() {
        updateRangeInputs(); // Mettre à jour les éléments de plage lorsque les valeurs des éléments de saisie de prix changent
        updatePriceInputs(); // Mettre à jour les éléments de saisie de prix également
    });
});

rangeInput.forEach(input => {
    input.addEventListener("input", updatePriceInputs); // Mettre à jour les éléments de saisie de prix lorsque les valeurs des éléments de plage changent
});
