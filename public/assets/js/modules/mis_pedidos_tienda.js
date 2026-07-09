   document.addEventListener('DOMContentLoaded', function() {

            const searchInput = document.getElementById('storeOrdersSearch');
            const statusSelect = document.getElementById('storeOrdersStatus');
            const cards = document.querySelectorAll('[data-order-card]');
            const noResults = document.getElementById('storeOrdersNoResults');

            function filterOrders() {
                const search = (searchInput?.value || '').toLowerCase().trim();
                const status = statusSelect?.value || '';

                let visible = 0;

                cards.forEach(card => {
                    const cardSearch = card.dataset.search || '';
                    const cardStatus = card.dataset.status || '';

                    const matchesSearch = !search || cardSearch.includes(search);
                    const matchesStatus = !status || cardStatus === status;

                    if (matchesSearch && matchesStatus) {
                        card.classList.remove('d-none');
                        visible++;
                    } else {
                        card.classList.add('d-none');
                    }
                });

                if (noResults) {
                    noResults.classList.toggle('d-none', visible > 0);
                }
            }

            searchInput?.addEventListener('input', filterOrders);
            statusSelect?.addEventListener('change', filterOrders);

        });