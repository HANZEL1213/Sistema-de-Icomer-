
document.addEventListener('DOMContentLoaded', function () {

    const qtyButtons = document.querySelectorAll('[data-cart-qty]');

    qtyButtons.forEach((button) => {

        button.addEventListener('click', function () {

            const targetId = this.dataset.target;
            const action = this.dataset.cartQty;

            const input = document.getElementById(targetId);

            if (!input) return;

            const form = input.closest('form');

            const min = parseInt(input.min || 1);
            const max = parseInt(input.max || 999);

            let value = parseInt(input.value || 1);

            if (action === 'minus') {
                value = Math.max(min, value - 1);
            }

            if (action === 'plus') {
                value = Math.min(max, value + 1);
            }

            input.value = value;

            form.submit();

        });

    });

    const qtyInputs = document.querySelectorAll('.store-cart-qty-control input');

    qtyInputs.forEach((input) => {

        input.addEventListener('change', function () {

            const form = this.closest('form');

            form.submit();

        });

    });

});
