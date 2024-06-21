let checkboxes = document.querySelectorAll(".checkBoxAbsence");
let form = document.getElementById('abcenseForm');
let submitButton = document.getElementsByClassName('submitButton')[0];
let messageError = document.getElementById('messageError');

// Adiciona um evento de clique ao botão de envio
submitButton.addEventListener('click', function(event) {
    let ver = false;
    // Previne o comportamento padrão do botão de envio
    event.preventDefault();

    // Percorre todos os checkboxes e verifica se algum está marcado
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked === true) {
            ver = true;
        }
    });

    if (ver === true) {
        // Submete o formulário
        form.submit();
    } else {
        messageError.innerHTML = "Por favor selecione alguma falta antes de clicar em Justificar";
    }
});

let modified = false; // Variável para rastrear se as células foram modificadas

// Função para adicionar labels
function addLabels(cells, label) {
    cells.forEach(cell => {
        if (!cell.dataset.original) {
            cell.dataset.original = cell.innerHTML; // Armazena o conteúdo original
        }
        if (!cell.dataset.modified || cell.dataset.modified === 'false') {
            cell.innerHTML = `<span class="label">${label}</span>${cell.dataset.original}`;
            cell.dataset.modified = 'true'; // Marca a célula como modificada
        }
    });
}

// Função para remover labels
function removeLabels(cells, label) {
    cells.forEach(cell => {
        if (cell.dataset.modified === 'true') {
            cell.innerHTML = cell.dataset.original;
            cell.dataset.modified = 'false'; // Marca a célula como não modificada
        }
    });
}

function handleResize() {
    let idCells = document.querySelectorAll('.idCell');
    let absenceStartCells = document.querySelectorAll('.absenceStartCell');
    let absenceEndCells = document.querySelectorAll('.absenceEndCell');
    let absenceTypeCells = document.querySelectorAll('.absenceTypeCell');
    let absenceStateCells = document.querySelectorAll('.absenceStateCell');
    let justificationStateCells = document.querySelectorAll('.justificationStateCell');

    if (window.innerWidth <= 1000 && !modified) {
        addLabels(idCells, 'Id: ');
        addLabels(absenceStartCells, 'Data Inicio Falta: ');
        addLabels(absenceEndCells, 'Data Fim Falta: ');
        addLabels(absenceTypeCells, 'Tipo Falta: ');
        addLabels(absenceStateCells, 'Estado Falta: ');
        addLabels(justificationStateCells, 'Estado Justificação: ');

        modified = true;
    } else if (window.innerWidth > 1000 && modified) {
        removeLabels(idCells, 'Id: ');
        removeLabels(absenceStartCells, 'Data Inicio Falta: ');
        removeLabels(absenceEndCells, 'Data Fim Falta: ');
        removeLabels(absenceTypeCells, 'Tipo Falta: ');
        removeLabels(absenceStateCells, 'Estado Falta: ');
        removeLabels(justificationStateCells, 'Estado Justificação: ');

        modified = false;
    }
}

// Adiciona o evento de resize ao carregar a página
window.addEventListener('resize', handleResize);

// Executa a função uma vez ao carregar a página para verificar a largura inicial
handleResize();
