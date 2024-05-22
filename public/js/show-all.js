document.addEventListener('DOMContentLoaded', function () {
    const search = document.querySelector('.input-group input'),
        table_rows = document.querySelectorAll('tbody tr'),
        table_headings = document.querySelectorAll('thead th');

    //  Evento de input para a busca na tabela
    search.addEventListener('input', searchTable);

    function searchTable() {
        table_rows.forEach((row, i) => {
            let table_data = row.textContent.toLowerCase(),
                search_data = search.value.toLowerCase();

            // Esconde a linha se os dados não correspondem à busca
            row.classList.toggle('hide', table_data.indexOf(search_data) < 0);
            row.style.setProperty('--delay', i / 25 + 's');
        })

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
            })

            // Alterna a classe 'asc' para determinar a ordem de classificação
            head.classList.toggle('asc', sort_asc);
            sort_asc = !head.classList.contains('asc');

            sortTable(i, sort_asc);
        }
    })

    // Função para ordenar a tabela
    function sortTable(column, sort_asc) {
        [...table_rows].sort((a, b) => {
            let first_row = a.querySelectorAll('td')[column].textContent.toLowerCase(),
                second_row = b.querySelectorAll('td')[column].textContent.toLowerCase();

            return sort_asc ? (first_row < second_row ? 1 : -1) : (first_row < second_row ? -1 : 1);
        })
            .map(sorted_row => document.querySelector('tbody').appendChild(sorted_row));
    }

    //Converte a tabela HTML para PDF

    const pdf_btn = document.querySelector('#toPDF');
    const users_table = document.querySelector('#users_table');

    const toPDF = function (users_table) {
        // Clonar a tabela original
        const table_clone = users_table.cloneNode(true);

        // Remover as últimas duas colunas da tabela clonada (delete e edit)
        const rows = table_clone.querySelectorAll('tr');
        rows.forEach(row => {
            row.removeChild(row.children[row.children.length - 1]);
            row.removeChild(row.children[row.children.length - 1]);
        });

        const html_code = `
        <!DOCTYPE html>
        <link rel="stylesheet" type="text/css" href="style.css">
        <main class="table" id="users_table">${table_clone.innerHTML}</main>`;

        const new_window = window.open();
        new_window.document.write(html_code);

        setTimeout(() => {
            new_window.print();
            new_window.close();
        }, 400);
    }

    pdf_btn.onclick = () => {
        toPDF(users_table);
    }

    //Converte a tabela HTML para JSON

    const json_btn = document.querySelector('#toJSON');

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
            })
            table_data.push(row_object);
        })

        return JSON.stringify(table_data, null, 4);
    }

    json_btn.onclick = () => {
        const json = toJSON(users_table);
        downloadFile(json, 'json')
    }

    // Converte a tabela HTML para CSV

    const csv_btn = document.querySelector('#toCSV');

    const toCSV = function (table) {
        const t_heads = table.querySelectorAll('th'),
            tbody_rows = table.querySelectorAll('tbody tr');

        // Captura os cabeçalhos da tabela e formata para CSV, excluindo as duas últimas colunas
        const headings = [...t_heads].map((head, index) => {
            if (index < t_heads.length - 2) {
                return head.textContent.trim().toLowerCase();
            }
        }).filter(Boolean).join(',');

        // Captura os dados das linhas da tabela e formata para CSV, excluindo as duas últimas colunas
        const table_data = [...tbody_rows].map(row => {
            const cells = row.querySelectorAll('td');
            return [...cells].map((cell, index) => {
                if (index < cells.length - 2) {
                    return cell.textContent.replace(/,/g, ".").trim();
                }
            }).filter(Boolean).join(',');
        }).join('\n');

        return headings + '\n' + table_data;
    }

    csv_btn.onclick = () => {
        const csv = toCSV(users_table);
        downloadFile(csv, 'csv', 'user_data');
    }

    //Converte a tabela HTML para EXCEL

    const excel_btn = document.querySelector('#toEXCEL');

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
    }

    excel_btn.onclick = () => {
        const excel = toExcel(users_table);
        downloadFile(excel, 'excel');
    }

    // Função para baixar arquivos em diferentes formatos
    const downloadFile = function (data, fileType, fileName = '') {
        const a = document.createElement('a');
        a.download = fileName;
        const mime_types = {
            'json': 'application/json',
            'csv': 'text/csv',
            'excel': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        }
        a.href = `
            data:${mime_types[fileType]};charset=utf-8,${encodeURIComponent(data)}
        `;
        document.body.appendChild(a);
        a.click();
        a.remove();
    }
});
