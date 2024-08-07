document.addEventListener('DOMContentLoaded', function () {
    const search = document.querySelector('.input-group input'),
        table_rows = document.querySelectorAll('tbody tr'),
        table_headings = document.querySelectorAll('thead th');

    const monthFilter = document.getElementById('monthFilter');
    const yearFilter = document.getElementById('yearFilter');
    const presenceTableBody = document.querySelector('#work_times_table tbody');

    const months = {
        'Janeiro': '01', 'Fevereiro': '02', 'Março': '03', 'Abril': '04',
        'Maio': '05', 'Junho': '06', 'Julho': '07', 'Agosto': '08',
        'Setembro': '09', 'Outubro': '10', 'Novembro': '11', 'Dezembro': '12'
    };

    function filterTable() {
        const selectedMonth = monthFilter.value;
        const selectedYear = yearFilter.value;

        Array.from(presenceTableBody.querySelectorAll('tr')).forEach((row, i) => {
            const date = row.getAttribute('data-date');
            if (!date) {
                row.classList.add('hide');
                row.style.display = 'none';
                return;
            }
            const [year, month, day] = date.split('-');

            const match = (selectedMonth === '' || months[selectedMonth] === month) &&
                (selectedYear === '' || selectedYear === year);

            row.classList.toggle('hide', !match);
            row.style.setProperty('--delay', i / 25 + 's');

            setTimeout(() => {
                if (row.classList.contains('hide')) {
                    row.style.display = 'none';
                } else {
                    row.style.display = 'table-row';
                }
            }, 1000);
        });

        document.querySelectorAll('tbody tr:not(.hide)').forEach((visible_row, i) => {
            visible_row.style.backgroundColor = (i % 2 === 0) ? 'transparent' : '#0000000b';
        });
    }

    monthFilter.addEventListener('change', filterTable);
    yearFilter.addEventListener('change', filterTable);

    // Evento de input para a busca na tabela
    search.addEventListener('input', searchTable);

    function searchTable() {
        table_rows.forEach((row, i) => {
            let table_data = row.textContent.toLowerCase(),
                search_data = search.value.toLowerCase();

            row.classList.toggle('hide', table_data.indexOf(search_data) < 0);
            row.style.setProperty('--delay', i / 25 + 's');

            setTimeout(() => {
                if (row.classList.contains('hide')) {
                    row.style.display = 'none';
                } else {
                    row.style.display = 'table-row';
                }
            }, 1000);
        });

        document.querySelectorAll('tbody tr:not(.hide)').forEach((visible_row, i) => {
            visible_row.style.backgroundColor = (i % 2 === 0) ? 'transparent' : '#0000000b';
        });
    }

    // Evento de clique para ordenar os dados da tabela
    table_headings.forEach((head, i) => {
        let sort_asc = true;
        head.onclick = () => {
            table_headings.forEach(head => head.classList.remove('active'));
            head.classList.add('active');

            document.querySelectorAll('td').forEach(td => td.classList.remove('active'));
            table_rows.forEach(row => {
                row.querySelectorAll('td')[i].classList.add('active');
            });

            head.classList.toggle('asc', sort_asc);
            sort_asc = !head.classList.contains('asc');

            sortTable(i, sort_asc);
        };
    });

    function sortTable(column, sort_asc) {
        [...table_rows].sort((a, b) => {
            let first_row = a.querySelectorAll('td')[column].textContent.toLowerCase(),
                second_row = b.querySelectorAll('td')[column].textContent.toLowerCase();

            return sort_asc ? (first_row < second_row ? 1 : -1) : (first_row < second_row ? -1 : 1);
        })
            .map(sorted_row => document.querySelector('tbody').appendChild(sorted_row));
    }

    // Função para remover setas dos cabeçalhos
    function removeArrowsFromHeaders(table) {
        const t_headings = table.querySelectorAll('th');
        t_headings.forEach(th => {
            const text = th.childNodes[0].nodeValue.trim();
            th.textContent = text;
        });
    }

    // Funções de exportação
    const pdf_btn = document.querySelector('#toPDF');
    const json_btn = document.querySelector('#toJSON');
    const csv_btn = document.querySelector('#toCSV');
    const excel_btn = document.querySelector('#toEXCEL');
    const work_times_table = document.querySelector('#work_times_table');

    // Função para remover a última coluna
    function removeLastColumn(table) {
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.children.length > 0) {
                row.removeChild(row.lastElementChild);
            }
        });
    }

    // Converte a tabela HTML para PDF
    const toPDF = function (work_times_table) {
        const table_clone = work_times_table.cloneNode(true);

        // Remover as setas dos cabeçalhos e a última coluna
        removeArrowsFromHeaders(table_clone);
        removeLastColumn(table_clone);

        const styles = `
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                color: #000;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
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
            tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tbody tr:hover {
                background-color: #f1f1f1 !important;
            }
        </style>`;

        const html_code = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Table PDF</title>
            ${styles}
        </head>
        <body>
            <main class="table" id="work_times_table">${table_clone.outerHTML}</main>
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
        toPDF(work_times_table);
    };

    // Converte a tabela HTML para JSON
    const toJSON = function (table) {
        let table_data = [],
            t_head = [],
            t_headings = table.querySelectorAll('th'),
            t_rows = table.querySelectorAll('tbody tr');

        // Captura todos os cabeçalhos da tabela e remove as setas
        t_headings.forEach((t_heading, index) => {
            if (index < t_headings.length - 1) { // Ignora a última coluna
                let actual_head = t_heading.childNodes[0].nodeValue.trim(); // Captura o texto sem as setas
                t_head.push(actual_head.toLowerCase());
            }
        });

        // Captura todos os dados das linhas da tabela
        t_rows.forEach(row => {
            const row_object = {},
                t_cells = row.querySelectorAll('td');

            // Mapeia cada célula para o respectivo cabeçalho
            t_cells.forEach((t_cell, cell_index) => {
                if (cell_index < t_cells.length - 1) { // Ignora a última coluna
                    row_object[t_head[cell_index]] = t_cell.textContent.trim();
                }
            });
            table_data.push(row_object);
        });

        return JSON.stringify(table_data, null, 4);
    };

    json_btn.onclick = () => {
        const json = toJSON(work_times_table);
        downloadFile(json, 'json', 'work_times_data.json');
    };

    // Converte a tabela HTML para CSV
    const toCSV = function (table) {
        const t_heads = table.querySelectorAll('th'),
            tbody_rows = table.querySelectorAll('tbody tr');

        // Captura os cabeçalhos da tabela e formata para CSV, removendo setas e ignorando a última coluna
        const headings = [...t_heads].slice(0, -1).map(head => head.childNodes[0].nodeValue.trim().toLowerCase()).join(',');

        // Captura os dados das linhas da tabela e formata para CSV, ignorando a última coluna
        const table_data = [...tbody_rows].map(row => {
            const cells = row.querySelectorAll('td');
            return [...cells].slice(0, -1).map(cell => cell.textContent.replace(/,/g, ".").trim()).join(',');
        }).join('\n');

        return headings + '\n' + table_data;
    };

    csv_btn.onclick = () => {
        const csv = toCSV(work_times_table);
        downloadFile(csv, 'csv', 'work_times_data.csv');
    };

    // Converte a tabela HTML para EXCEL
    const toExcel = function (table) {
        const workbook = XLSX.utils.book_new();
        const worksheet_data = [];

        const t_heads = table.querySelectorAll('th');
        const tbody_rows = table.querySelectorAll('tbody tr');

        // Captura os cabeçalhos da tabela e adiciona ao excel, ignorando a última coluna
        const headers = [...t_heads].slice(0, -1).map(head => head.childNodes[0].nodeValue.trim());
        worksheet_data.push(headers);

        // Captura os dados das linhas da tabela e adiciona ao excel, ignorando a última coluna
        [...tbody_rows].forEach(row => {
            const cells = row.querySelectorAll('td');
            const row_data = [...cells].slice(0, -1).map(cell => cell.textContent.trim());
            worksheet_data.push(row_data);
        });

        const worksheet = XLSX.utils.aoa_to_sheet(worksheet_data);
        XLSX.utils.book_append_sheet(workbook, worksheet, 'WorkTimes');

        // Gera o arquivo Excel
        XLSX.writeFile(workbook, 'work_times_data.xlsx');
    };

    excel_btn.onclick = () => {
        toExcel(work_times_table);
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

    // Código para o modal
    var modal = document.getElementById("workTimeModal");
    var buttons = document.querySelectorAll(".openModal");
    var span = document.getElementsByClassName("close")[0];

    buttons.forEach(button => {
        button.onclick = function() {
            var userId = this.getAttribute('data-user-id');
            var userName = this.getAttribute('data-user-name');
            document.getElementById('modal_user_id').value = userId;
            document.getElementById('modal_user_name').textContent = userName;
            modal.style.display = "block";
        }
    });

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
});
