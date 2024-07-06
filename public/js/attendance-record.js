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

    // evento de clique para ordenar os dados da tabela
    table_headings.forEach((head, i) => {
        let sort_asc = true;
        head.onclick = () => {
            // Remove a classe 'active' de todos os cabeçalhos e adiciona ao clicado
            table_headings.forEach(head => head.classList.remove('active'));
            head.classList.add('active');

            // Remove a classe 'active' de todas as células e adiciona às da coluna clicada
            document.querySelectorAll('td').forEach(td => td.classList.remove('active'));
            table_rows.forEach(row => {
                row.querySelectorAll('td')[i].classList.add('active');
            });

            // Alterna a classe 'asc' para determinar a ordem de classificação
            head.classList.toggle('asc', sort_asc);
            sort_asc = !head.classList.contains('asc');

            sortTable(i, sort_asc);
        };
    });

    // Função para ordenar a tabela
    function sortTable(column, sort_asc) {
        [...table_rows].sort((a, b) => {
            let first_row = a.querySelectorAll('td')[column].textContent.toLowerCase(),
                second_row = b.querySelectorAll('td')[column].textContent.toLowerCase();

            return sort_asc ? (first_row < second_row ? 1 : -1) : (first_row < second_row ? -1 : 1);
        })
            .map(sorted_row => document.querySelector('tbody').appendChild(sorted_row));
    }

    // Converte a tabela HTML para PDF
    const pdf_btn = document.querySelector('#toPDF');
    const users_table = document.querySelector('#users_table');

    const toPDF = function (users_table) {
        // Clonar a tabela original
        const table_clone = users_table.cloneNode(true);

        // Remover as setas dos cabeçalhos
        const t_headings = table_clone.querySelectorAll('th');
        t_headings.forEach(th => {
            const text = th.childNodes[0].nodeValue.trim();
            th.textContent = text;
        });

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
            .table__header .export__file {
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
        <main class="table" id="users_table">${table_clone.innerHTML}</main>
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
        toPDF(users_table);
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
            let actual_head = t_heading.childNodes[0].nodeValue.trim().toLowerCase();
            let unique_head = actual_head;
            let counter = 1;

            // Garante que o cabeçalho seja único adicionando um índice, se necessário
            while (t_head.includes(unique_head)) {
                unique_head = `${actual_head}_${counter}`;
                counter++;
            }

            t_head.push(unique_head);
        });

        console.log('Cabeçalhos processados:', t_head);

        // Captura todos os dados das linhas da tabela
        t_rows.forEach(row => {
            const row_object = {},
                t_cells = row.querySelectorAll('td');

            console.log('Células da linha:', t_cells);

            // Mapeia cada célula para o respectivo cabeçalho
            t_cells.forEach((t_cell, cell_index) => {
                row_object[t_head[cell_index]] = t_cell.textContent.trim();
            });

            console.log('Objeto da linha:', row_object);

            table_data.push(row_object);
        });

        console.log('Dados da tabela:', table_data);

        return JSON.stringify(table_data, null, 4);
    };

    json_btn.onclick = () => {
        const json = toJSON(users_table);
        downloadFile(json, 'json', 'user_data.json');
    };



    // Converte a tabela HTML para CSV
    const csv_btn = document.querySelector('#toCSV');

    const toCSV = function (table) {
        const t_heads = table.querySelectorAll('th'),
            tbody_rows = table.querySelectorAll('tbody tr');

        // Captura os cabeçalhos da tabela e formata para CSV, removendo setas
        const headings = [...t_heads].map(head => head.childNodes[0].nodeValue.trim().toLowerCase()).join(',');

        // Captura os dados das linhas da tabela e formata para CSV
        const table_data = [...tbody_rows].map(row => {
            const cells = row.querySelectorAll('td');
            return [...cells].map(cell => cell.textContent.replace(/,/g, ".").trim()).join(',');
        }).join('\n');

        return headings + '\n' + table_data;
    };

    csv_btn.onclick = () => {
        const csv = toCSV(users_table);
        downloadFile(csv, 'csv', 'user_data.csv');
    };


    // Converte a tabela HTML para EXCEL usando SheetJS
    const excel_btn = document.querySelector('#toEXCEL');

    const toExcel = function (table) {
        const workbook = XLSX.utils.book_new();
        const worksheet_data = [];

        const t_heads = table.querySelectorAll('th');
        const tbody_rows = table.querySelectorAll('tbody tr');

        // Captura os cabeçalhos da tabela e adiciona ao excel, removendo setas
        const headers = [...t_heads].map(head => head.childNodes[0].nodeValue.trim());
        worksheet_data.push(headers);

        // Captura os dados das linhas da tabela e adiciona ao excel
        [...tbody_rows].forEach(row => {
            const cells = row.querySelectorAll('td');
            const row_data = [...cells].map(cell => cell.textContent.trim());
            worksheet_data.push(row_data);
        });

        const worksheet = XLSX.utils.aoa_to_sheet(worksheet_data);
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Users');

        // Gera o arquivo Excel
        XLSX.writeFile(workbook, 'user_data.xlsx');
    };

    excel_btn.onclick = () => {
        toExcel(users_table);
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
