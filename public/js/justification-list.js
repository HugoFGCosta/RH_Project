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
    let usernameCells = document.querySelectorAll('.usernameCell');
    let motiveCells = document.querySelectorAll('.motiveCell');
    let justificationDateCells = document.querySelectorAll('.justificationDateCell');
    let observationCells = document.querySelectorAll('.observationCell');
    let stateCells = document.querySelectorAll('.stateCell');

    if (window.innerWidth <= 1000 && !modified) {
        addLabels(usernameCells, 'Nome: ');
        addLabels(motiveCells, 'Motivo: ');
        addLabels(justificationDateCells, 'Data Justificação: ');
        addLabels(observationCells, 'Observações: ');
        addLabels(stateCells, 'Estado: ');
        modified = true;
    } else if (window.innerWidth > 1000 && modified) {
        removeLabels(usernameCells, 'Nome: ');
        removeLabels(motiveCells, 'Motivo: ');
        removeLabels(justificationDateCells, 'Data Justificação: ');
        removeLabels(observationCells, 'Observações: ');
        removeLabels(stateCells, 'Estado: ');
        modified = false;
    }
}

// Adiciona o evento de resize ao carregar a página
window.addEventListener('resize', handleResize);

// Executa a função uma vez ao carregar a página para verificar a largura inicial
handleResize();
