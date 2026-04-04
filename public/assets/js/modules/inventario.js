
    document.addEventListener('DOMContentLoaded', function () {
        const pedidoSelect = document.getElementById('id_pedido');
        const ventaLocalSelect = document.getElementById('id_venta_local');

        function syncReferenciaExclusiva() {
            if (!pedidoSelect || !ventaLocalSelect) return;

            pedidoSelect.addEventListener('change', function () {
                if (this.value) {
                    ventaLocalSelect.value = '';
                }
            });

            ventaLocalSelect.addEventListener('change', function () {
                if (this.value) {
                    pedidoSelect.value = '';
                }
            });
        }

        syncReferenciaExclusiva();
    });
