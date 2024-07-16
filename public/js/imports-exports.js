// Obtém o modal
let modal = document.getElementById("helpModal");

// Obtém o botão que abre o modal
let btn = document.getElementById("openModal");

// Obtém o elemento <span> que fecha o modal
let span = document.getElementsByClassName("close")[0];

// Quando o utilizador clica no botão, abre o modal
btn.onclick = function() {
    modal.style.display = "block";
}

// Quando o utilizador clica no <span> (x), fecha o modal
span.onclick = function() {
    modal.style.display = "none";
}

// Quando o utilizador clica em qualquer lugar fora do modal, fecha-o
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
