/* ==========================================================
   LOGIN ADMIN
========================================================== */

document.addEventListener('DOMContentLoaded', function () {

    const loginForm = document.getElementById('storeLoginForm');
    const registerForm = document.getElementById('storeRegisterForm');
    const showRegisterForm = document.getElementById('showRegisterForm');
    const showLoginForm = document.getElementById('showLoginForm');

    if (showRegisterForm && showLoginForm && loginForm && registerForm) {
        showRegisterForm.addEventListener('click', function () {
            loginForm.classList.remove('is-active');
            registerForm.classList.add('is-active');
        });

        showLoginForm.addEventListener('click', function () {
            registerForm.classList.remove('is-active');
            loginForm.classList.add('is-active');
        });
    }

    document.querySelectorAll('.store-auth-password-toggle').forEach(button => {
        button.addEventListener('click', function () {
            const input = this.closest('.store-auth-input').querySelector('input');
            const icon = this.querySelector('i');

            input.type = input.type === 'password' ? 'text' : 'password';

            icon.classList.toggle('bx-show');
            icon.classList.toggle('bx-hide');
        });
    });

	document.querySelectorAll(
    '.admin-auth-password-toggle'
).forEach(button => {

    button.addEventListener('click', function () {

        const input = this.closest(
            '.admin-auth-input'
        ).querySelector('input');

        const icon = this.querySelector('i');

        input.type =
            input.type === 'password'
                ? 'text'
                : 'password';

        icon.classList.toggle('bx-show');
        icon.classList.toggle('bx-hide');

    });

});


/* ==========================================================
   ALERT LOGIN
========================================================== */

const adminAlert = document.getElementById('adminAlert');
const closeAdminAlert = document.getElementById('closeAdminAlert');

if (closeAdminAlert) {

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

/* ==========================================================
   LOGIN TIENDA
========================================================== */