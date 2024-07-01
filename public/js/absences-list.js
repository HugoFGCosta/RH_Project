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
    let nameCells = document.querySelectorAll('.namCell');
    let dateAbsenceCells = document.querySelectorAll('.dateAbsenceCell');
    let motiveCells = document.querySelectorAll('.motiveCell');
    let durationCells = document.querySelectorAll('.durationCell');
    let stateCells = document.querySelectorAll('.stateCell');
    let dateJustificationCells = document.querySelectorAll('.dateJustificationCell');
    let observationCells = document.querySelectorAll('.observationCell');
    let buttonCells = document.querySelectorAll('.buttonCell');

    if (window.innerWidth <= 1000 && !modified) {
        addLabels(idCells, 'Id: ');
        addLabels(nameCells, 'Name: ');
        addLabels(dateAbsenceCells, 'Data Falta: ');
        addLabels(motiveCells, 'Motivo: ');
        addLabels(durationCells, 'Duração: ');
        addLabels(stateCells, 'Estado: ');
        addLabels(dateJustificationCells, 'Data Justificação: ');
        addLabels(observationCells, 'Observações: ');
        addLabels(buttonCells, 'Ações: ');

        modified = true;
    } else if (window.innerWidth > 1000 && modified) {
        removeLabels(idCells, 'Id: ');
        removeLabels(nameCells, 'Name: ');
        removeLabels(dateAbsenceCells, 'Data Falta: ');
        removeLabels(motiveCells, 'Motivo: ');
        removeLabels(durationCells, 'Duração : ');
        removeLabels(stateCells, 'Estado: ');
        removeLabels(dateJustificationCells, 'Data Justificação: ');
        removeLabels(observationCells, 'Observações: ');
        removeLabels(buttonCells, 'Ações: ');


        modified = false;
    }
}

// Adiciona o evento de resize ao carregar a página
window.addEventListener('resize', handleResize);

// Executa a função uma vez ao carregar a página para verificar a largura inicial
handleResize();
