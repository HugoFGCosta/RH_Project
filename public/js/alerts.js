// public/js/alerts.js
document.addEventListener('DOMContentLoaded', function () {
    const modalContainer = document.getElementById('modal-container');
    const closeBtn = document.getElementById('close-btn');

    if (modalContainer) {
        closeBtn.addEventListener('click', function () {
            modalContainer.style.display = 'none';
        });

        // Fechar o modal clicando fora dele
        window.addEventListener('click', function (event) {
            if (event.target == modalContainer) {
                modalContainer.style.display = 'none';
            }
        });

        // Fechar o modal após 5 segundos
        setTimeout(() => {
            modalContainer.style.display = 'none';
        }, 5000);
    }
});

