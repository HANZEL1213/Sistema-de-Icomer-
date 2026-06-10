document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR / OCULTAR CONTRASEÑA TIENDA
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.store-auth-password-toggle').forEach(button => {

        button.addEventListener('click', function () {

            const input = this.closest('.store-auth-input').querySelector('input');
            const icon = this.querySelector('i');

            input.type = input.type === 'password' ? 'text' : 'password';

            icon.classList.toggle('bx-show');
            icon.classList.toggle('bx-hide');

        });

    });


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR / OCULTAR CONTRASEÑA ADMIN
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.admin-auth-password-toggle').forEach(button => {

        button.addEventListener('click', function () {

            const input = this.closest('.admin-auth-input').querySelector('input');
            const icon = this.querySelector('i');

            input.type = input.type === 'password' ? 'text' : 'password';

            icon.classList.toggle('bx-show');
            icon.classList.toggle('bx-hide');

        });

    });


    /*
    |--------------------------------------------------------------------------
    | ALERT LOGIN ADMIN
    |--------------------------------------------------------------------------
    */

    const adminAlert = document.getElementById('adminAlert');
    const closeAdminAlert = document.getElementById('closeAdminAlert');

    if (adminAlert && closeAdminAlert) {

        closeAdminAlert.addEventListener('click', () => {
            adminAlert.remove();
        });

        setTimeout(() => {
            if (adminAlert) {
                adminAlert.remove();
            }
        }, 4000);

    }

});