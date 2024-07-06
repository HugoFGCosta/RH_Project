document.addEventListener('DOMContentLoaded', function () {
    const search = document.querySelector('.input-group input'),
        table_rows = document.querySelectorAll('tbody tr'),
        table_headings = document.querySelectorAll('thead th');

    // Evento de input para a busca na tabela
    search.addEventListener('input', searchTable);

    function searchTable() {
        table_rows.forEach((row, i) => {
            let table_data = row.textContent.toLowerCase(),
                search_data = search.value.toLowerCase();

            // Esconde a linha se os dados não correspondem à busca
            row.classList.toggle('hide', table_data.indexOf(search_data) < 0);
            row.style.setProperty('--delay', i / 25 + 's');

            // depois de esconder a linha remove-a
            setTimeout(() => {
                if (row.classList.contains('hide')) {
                    row.style.display = 'none';
                } else {
                    row.style.display = 'table-row';
                }
            }, 1000);
        });

        // Altera a cor de fundo das linhas visíveis
        document.querySelectorAll('tbody tr:not(.hide)').forEach((visible_row, i) => {
            visible_row.style.backgroundColor = (i % 2 === 0) ? 'transparent' : '#0000000b';
        });
    }

    // Evento de clique para ordenar os dados da tabela
    table_headings.forEach((head, i) => {
        let sort_asc = true;
        head.addEventListener('click', () => {
            // Remove a classe 'active' de todos os cabeçalhos e adiciona ao clicado
            table_headings.forEach(head => head.classList.remove('active', 'asc', 'desc'));
            head.classList.add('active');
            head.classList.toggle('asc', sort_asc);
            head.classList.toggle('desc', !sort_asc);

            // Remove a classe 'active' de todas as células e adiciona às da coluna clicada
            document.querySelectorAll('td').forEach(td => td.classList.remove('active'));
            table_rows.forEach(row => {
                row.querySelectorAll('td')[i].classList.add('active');
            });

            sortTable(i, sort_asc);
            sort_asc = !sort_asc;
        });
    });

    // Função para ordenar a tabela
    function sortTable(column, sort_asc) {
        const rows = Array.from(table_rows);

        rows.sort((a, b) => {
            const first_row = a.querySelectorAll('td')[column].textContent.toLowerCase();
            const second_row = b.querySelectorAll('td')[column].textContent.toLowerCase();

            if (first_row < second_row) return sort_asc ? -1 : 1;
            if (first_row > second_row) return sort_asc ? 1 : -1;
            return 0;
        });

        rows.forEach(row => document.querySelector('tbody').appendChild(row));
    }

    // Função para remover as setas dos cabeçalhos
    function removeArrowsFromHeaders(table) {
        const t_headings = table.querySelectorAll('th');
        t_headings.forEach(th => {
            const text = th.childNodes[0].nodeValue.trim();
            th.textContent = text;
        });
    }

    // Função para remover a coluna de edição e apagar
    function removeEditDeleteColumns(table) {
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.children.length > 1) {
                row.removeChild(row.lastElementChild); // Remove a última coluna (Apagar)
                row.removeChild(row.lastElementChild); // Remove a penúltima coluna (Editar)
            }
        });
    }

    // Converte a tabela HTML para PDF
    const pdf_btn = document.querySelector('#toPDF');
    const vacations_table = document.querySelector('#vacations_table');

    const toPDF = function (table) {
        // Clonar a tabela original
        const table_clone = table.cloneNode(true);

        // Remover as setas dos cabeçalhos
        removeArrowsFromHeaders(table_clone);

        // Remover as duas últimas colunas (Editar e Apagar)
        removeEditDeleteColumns(table_clone);

        // Adicionar estilos específicos para a impressão
        const styles = `
    <style>
        @media print {
            body {
                margin: 0;
                font-family: Arial, sans-serif;
                font-size: 12px;
                color: #000;
            }

            .table__header .input-group,
            .table__header .export__file,
            .table__header .new__vacation {
                display: none !important;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
                padding: 0;
                table-layout: fixed;
            }

            table, th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
                word-wrap: break-word;
            }

            thead th {
                background-color: #f2f2f2;
                color: #000;
                font-weight: bold;
            }

            tbody tr {
                background-color: #fff;
            }

            tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            tbody tr:hover {
                background-color: #f1f1f1 !important;
            }

            tbody tr td {
                vertical-align: top;
            }
        }
    </style>`;

        const html_code = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Table PDF</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        ${styles}
    </head>
    <body>
        <main class="table" id="vacations_table">${table_clone.innerHTML}</main>
    </body>
    </html>`;

        const new_window = window.open();
        new_window.document.write(html_code);

        setTimeout(() => {
            new_window.print();
            new_window.close();
        }, 400);
    };

    pdf_btn.onclick = () => {
        toPDF(vacations_table);
    };


    // Converte a tabela HTML para JSON
    const json_btn = document.querySelector('#toJSON');

    const toJSON = function (table) {
        let table_data = [],
            t_head = [],
            t_headings = table.querySelectorAll('th'),
            t_rows = table.querySelectorAll('tbody tr');

        // Captura todos os cabeçalhos da tabela e remove as setas
        t_headings.forEach((t_heading, index) => {
            if (index < t_headings.length - 2) { // Ignora as últimas duas colunas
                let actual_head = t_heading.childNodes[0].nodeValue.trim().toLowerCase();
                let unique_head = actual_head;
                let counter = 1;

                // Garante que o cabeçalho seja único adicionando um índice, se necessário
                while (t_head.includes(unique_head)) {
                    unique_head = `${actual_head}_${counter}`;
                    counter++;
                }

                t_head.push(unique_head);
            }
        });

        // Captura todos os dados das linhas da tabela
        t_rows.forEach(row => {
            const row_object = {},
                t_cells = row.querySelectorAll('td');

            // Mapeia cada célula para o respectivo cabeçalho
            t_cells.forEach((t_cell, cell_index) => {
                if (cell_index < t_cells.length - 2) { // Ignora as últimas duas colunas
                    row_object[t_head[cell_index]] = t_cell.textContent.trim();
                }
            });

            table_data.push(row_object);
        });

        return JSON.stringify(table_data, null, 4);
    };

    json_btn.onclick = () => {
        const json = toJSON(vacations_table);
        downloadFile(json, 'json', 'vacation_data.json');
    };

    // Converte a tabela HTML para CSV
    const csv_btn = document.querySelector('#toCSV');

    const toCSV = function (table) {
        const t_heads = table.querySelectorAll('th'),
            tbody_rows = table.querySelectorAll('tbody tr');

        // Captura os cabeçalhos da tabela e formata para CSV, removendo setas
        const headings = [...t_heads].slice(0, -2).map(head => head.childNodes[0].nodeValue.trim().toLowerCase()).join(',');

        // Captura os dados das linhas da tabela e formata para CSV
        const table_data = [...tbody_rows].map(row => {
            const cells = row.querySelectorAll('td');
            return [...cells].slice(0, -2).map(cell => cell.textContent.replace(/,/g, ".").trim()).join(',');
        }).join('\n');

        return headings + '\n' + table_data;
    };

    csv_btn.onclick = () => {
        const csv = toCSV(vacations_table);
        downloadFile(csv, 'csv', 'vacation_data.csv');
    };


    // Converte a tabela HTML para EXCEL usando SheetJS
    const excel_btn = document.querySelector('#toEXCEL');

    const toExcel = function (table) {
        const workbook = XLSX.utils.book_new();
        const worksheet_data = [];

        const t_heads = table.querySelectorAll('th');
        const tbody_rows = table.querySelectorAll('tbody tr');

        // Captura os cabeçalhos da tabela e adiciona ao excel, removendo as últimas duas colunas
        const headers = [...t_heads].slice(0, -2).map(head => head.childNodes[0].nodeValue.trim());
        worksheet_data.push(headers);

        // Captura os dados das linhas da tabela e adiciona ao excel, removendo as últimas duas colunas
        [...tbody_rows].forEach(row => {
            const cells = row.querySelectorAll('td');
            const row_data = [...cells].slice(0, -2).map(cell => cell.textContent.trim());
            worksheet_data.push(row_data);
        });

        const worksheet = XLSX.utils.aoa_to_sheet(worksheet_data);
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Vacations');

        // Gera o arquivo Excel
        XLSX.writeFile(workbook, 'vacation_data.xlsx');
    };

    excel_btn.onclick = () => {
        toExcel(vacations_table);
    };

    // Função para baixar arquivos em diferentes formatos
    const downloadFile = function (data, fileType, fileName) {
        const a = document.createElement('a');
        a.download = fileName;
        const mime_types = {
            'json': 'application/json',
            'csv': 'text/csv',
            'excel': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        };
        const blob = new Blob([data], { type: mime_types[fileType] + ';charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        a.href = url;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    // Fecha o modal se clicar fora dele
    document.addEventListener('click', (event) => {
        const exportFileCheckbox = document.querySelector('#export-file');
        const exportFileOptions = document.querySelector('.export__file-options');

        if (!exportFileOptions.contains(event.target) && event.target !== exportFileCheckbox && exportFileCheckbox.checked) {
            exportFileCheckbox.checked = false;
        }
    });
});

