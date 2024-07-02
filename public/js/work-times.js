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

    // Funções de exportação
    const pdf_btn = document.querySelector('#toPDF');
    const json_btn = document.querySelector('#toJSON');
    const csv_btn = document.querySelector('#toCSV');
    const excel_btn = document.querySelector('#toEXCEL');
    const work_times_table = document.querySelector('#work_times_table');

    // Converte a tabela HTML para PDF
    const toPDF = function (work_times_table) {
        const table_clone = work_times_table.cloneNode(true);
        const rows = table_clone.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.children.length > 2) {
                row.removeChild(row.children[row.children.length - 1]);
                row.removeChild(row.children[row.children.length - 1]);
            }
        });

        const html_code = `
        <!DOCTYPE html>
        <html>
        <head>
            <link rel="stylesheet" type="text/css" href="{{ asset('/css/work-times.css') }}">
        </head>
        <body>
            <main class="table" id="work_times_table">${table_clone.innerHTML}</main>
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

        t_headings.forEach((t_heading, index) => {
            if (index < t_headings.length - 2) {
                let actual_head = t_heading.textContent.trim();
                t_head.push(actual_head.toLowerCase());
            }
        });

        t_rows.forEach(row => {
            const row_object = {},
                t_cells = row.querySelectorAll('td');

            t_cells.forEach((t_cell, cell_index) => {
                if (cell_index < t_cells.length - 2) {
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

        const headings = [...t_heads].map((head, index) => {
            if (index < t_heads.length - 2) {
                return head.textContent.trim().toLowerCase();
            }
        }).filter(Boolean).join(',');

        const table_data = [...tbody_rows].map(row => {
            const cells = row.querySelectorAll('td');
            return [...cells].map((cell, index) => {
                if (index < cells.length - 2) {
                    return cell.textContent.replace(/,/g, ".").trim();
                }
            }).filter(Boolean).join(',');
        }).join('\n');

        return headings + '\n' + table_data;
    };

    csv_btn.onclick = () => {
        const csv = toCSV(work_times_table);
        downloadFile(csv, 'csv', 'work_times_data.csv');
    };

    // Converte a tabela HTML para EXCEL
    const toExcel = function (table) {
        const t_heads = table.querySelectorAll('th'),
            tbody_rows = table.querySelectorAll('tbody tr');

        const headings = [...t_heads].map((head, index) => {
            if (index < t_heads.length - 2) {
                return head.textContent.trim().toLowerCase();
            }
        }).filter(Boolean).join('\t');

        const table_data = [...tbody_rows].map(row => {
            const cells = row.querySelectorAll('td');
            return [...cells].map((cell, index) => {
                if (index < cells.length - 2) {
                    return cell.textContent.trim();
                }
            }).filter(Boolean).join('\t');
        }).join('\n');

        return headings + '\n' + table_data;
    };

    excel_btn.onclick = () => {
        const excel = toExcel(work_times_table);
        downloadFile(excel, 'excel', 'work_times_data.xls');
    };

    // Função para baixar arquivos em diferentes formatos
    const downloadFile = function (data, fileType, fileName = '') {
        const a = document.createElement('a');
        a.download = fileName;
        const mime_types = {
            'json': 'application/json',
            'csv': 'text/csv',
            'excel': 'application/vnd.ms-excel',
        };
        a.href = `
            data:${mime_types[fileType]};charset=utf-8,${encodeURIComponent(data)}
        `;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
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
