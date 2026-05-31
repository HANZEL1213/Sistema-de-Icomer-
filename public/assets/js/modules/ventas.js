
document.addEventListener('DOMContentLoaded', function () {
    const exportBtn = document.querySelector('.btn-nuevo');
    const table = document.getElementById('tabla_index');

    if (!exportBtn || !table) return;

    exportBtn.addEventListener('click', function () {
        const fecha = new Date().toLocaleDateString('es-CR');

        let html = `
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                    }

                    th {
                        background: #1f2937;
                        color: #ffffff;
                        font-weight: bold;
                        text-align: center;
                        border: 1px solid #d1d5db;
                        padding: 10px;
                    }

                    td {
                        border: 1px solid #d1d5db;
                        padding: 8px;
                        vertical-align: middle;
                    }

                    .titulo {
                        font-size: 20px;
                        font-weight: bold;
                        color: #111827;
                    }

                    .subtitulo {
                        font-size: 12px;
                        color: #6b7280;
                    }

                    .text-center {
                        text-align: center;
                    }

                    .text-right {
                        text-align: right;
                    }

                    .total {
                        font-weight: bold;
                        background: #f3f4f6;
                    }

                    .online {
                        background: #dcfce7;
                        color: #166534;
                        font-weight: bold;
                        text-align: center;
                    }

                    .fisica {
                        background: #fee2e2;
                        color: #991b1b;
                        font-weight: bold;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <table>
                    <tr>
                        <td colspan="8" class="titulo">Reporte de ventas</td>
                    </tr>
                    <tr>
                        <td colspan="8" class="subtitulo">Consolidado online + físicas</td>
                    </tr>
                    <tr>
                        <td colspan="8" class="subtitulo">Fecha de exportación: ${fecha}</td>
                    </tr>
                    <tr><td colspan="8"></td></tr>

                    <tr>
                        <th>ID</th>
                        <th>Canal</th>
                        <th>Referencia</th>
                        <th>Cliente / Cajero</th>
                        <th>Subtotal</th>
                        <th>Descuento</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
        `;

        table.querySelectorAll('tbody tr').forEach(tr => {
            if (tr.style.display === 'none') return;

            const cells = tr.querySelectorAll('td');

            if (cells.length < 8) return;

            const id = cleanText(cells[0].innerText);
            const canal = cleanText(cells[1].innerText);
            const referencia = cleanText(cells[2].innerText);
            const cliente = cleanText(cells[3].innerText);
            const subtotal = cleanMoney(cells[4].innerText);
            const descuento = cleanMoney(cells[5].innerText);
            const total = cleanMoney(cells[6].innerText);
            const fechaVenta = cleanText(cells[7].innerText);

            const canalClass = canal.toLowerCase().includes('online')
                ? 'online'
                : 'fisica';

            html += `
                <tr>
                    <td class="text-center">${escapeHtml(id)}</td>
                    <td class="${canalClass}">${escapeHtml(canal)}</td>
                    <td>${escapeHtml(referencia)}</td>
                    <td>${escapeHtml(cliente)}</td>
                    <td class="text-right">${escapeHtml(subtotal)}</td>
                    <td class="text-right">${escapeHtml(descuento)}</td>
                    <td class="text-right total">${escapeHtml(total)}</td>
                    <td class="text-center">${escapeHtml(fechaVenta)}</td>
                </tr>
            `;
        });

        html += `
                </table>
            </body>
            </html>
        `;

        const blob = new Blob(['\ufeff', html], {
            type: 'application/vnd.ms-excel;charset=utf-8;'
        });

        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');

        const nombreFecha = new Date().toISOString().slice(0, 10);

        link.href = url;
        link.download = `reporte_ventas_${nombreFecha}.xls`;

        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    });

    function cleanText(text) {
        return text
            .replace(/\s+/g, ' ')
            .trim();
    }

    function cleanMoney(text) {
        return text
            .replace(/\s+/g, ' ')
            .trim();
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
