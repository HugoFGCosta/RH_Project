document.addEventListener('DOMContentLoaded', function () {
    const search = document.querySelector('.input-group input'),
        table_rows = document.querySelectorAll('tbody tr'),
        table_headings = document.querySelectorAll('thead th');

    const monthFilter = document.getElementById('monthFilter');
    const yearFilter = document.getElementById('yearFilter');
    const presenceTableBody = document.querySelector('#users_table tbody');

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
            const [year, month] = date.split('-');

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

    // Search event for table
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

    // Click event to sort table data
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

    // Convert table to PDF
    const pdf_btn = document.querySelector('#toPDF');
    const users_table = document.querySelector('#users_table');

    const toPDF = function (users_table) {
        const table_clone = users_table.cloneNode(true);
        table_clone.querySelectorAll('.no-export').forEach(element => element.remove());

        const t_headings = table_clone.querySelectorAll('th');
        t_headings.forEach(th => {
            const text = th.childNodes[0].nodeValue.trim();
            th.textContent = text;
        });

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


    // Convert table to JSON
    const json_btn = document.querySelector('#toJSON');

    const toJSON = function (table) {
        const table_clone = table.cloneNode(true);
        table_clone.querySelectorAll('.no-export').forEach(element => element.remove());

        let table_data = [],
            t_head = [],
            t_headings = table_clone.querySelectorAll('th'),
            t_rows = table_clone.querySelectorAll('tbody tr');

        t_headings.forEach((t_heading, index) => {
            let actual_head = t_heading.childNodes[0].nodeValue.trim().toLowerCase();
            let unique_head = actual_head;
            let counter = 1;

            while (t_head.includes(unique_head)) {
                unique_head = `${actual_head}_${counter}`;
                counter++;
            }

            t_head.push(unique_head);
        });

        t_rows.forEach(row => {
            const row_object = {},
                t_cells = row.querySelectorAll('td');

            t_cells.forEach((t_cell, cell_index) => {
                row_object[t_head[cell_index]] = t_cell.textContent.trim();
            });

            table_data.push(row_object);
        });

        return JSON.stringify(table_data, null, 4);
    };

    json_btn.onclick = () => {
        const json = toJSON(users_table);
        downloadFile(json, 'json', 'user_data.json');
    };


    // Convert table to CSV
    const csv_btn = document.querySelector('#toCSV');

    const toCSV = function (table) {
        const table_clone = table.cloneNode(true);
        table_clone.querySelectorAll('.no-export').forEach(element => element.remove());

        const t_heads = table_clone.querySelectorAll('th'),
            tbody_rows = table_clone.querySelectorAll('tbody tr');

        const headings = [...t_heads].map(head => head.childNodes[0].nodeValue.trim().toLowerCase()).join(',');

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


    // Convert table to EXCEL using SheetJS
    const excel_btn = document.querySelector('#toEXCEL');

    const toExcel = function (table) {
        const table_clone = table.cloneNode(true);
        table_clone.querySelectorAll('.no-export').forEach(element => element.remove());

        const workbook = XLSX.utils.book_new();
        const worksheet_data = [];

        const t_heads = table_clone.querySelectorAll('th');
        const tbody_rows = table_clone.querySelectorAll('tbody tr');

        const headers = [...t_heads].map(head => head.childNodes[0].nodeValue.trim());
        worksheet_data.push(headers);

        [...tbody_rows].forEach(row => {
            const cells = row.querySelectorAll('td');
            const row_data = [...cells].map(cell => cell.textContent.trim());
            worksheet_data.push(row_data);
        });

        const worksheet = XLSX.utils.aoa_to_sheet(worksheet_data);
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Users');

        XLSX.writeFile(workbook, 'user_data.xlsx');
    };

    excel_btn.onclick = () => {
        toExcel(users_table);
    };


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

    document.addEventListener('click', (event) => {
        const exportFileCheckbox = document.querySelector('#export-file');
        const exportFileOptions = document.querySelector('.export__file-options');

        if (!exportFileOptions.contains(event.target) && event.target !== exportFileCheckbox && exportFileCheckbox.checked) {
            exportFileCheckbox.checked = false;
        }
    });
});
