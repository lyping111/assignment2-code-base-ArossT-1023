document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const username = form.querySelector('input[name="username"]');
            const password = form.querySelector('input[name="password"]');

            if (username && username.value.trim() === '') {
                alert('Please enter a username.');
                e.preventDefault();
                return;
            }

            if (password && password.value.trim() === '') {
                alert('Please enter a password.');
                e.preventDefault();
                return;
            }

            if (password && password.value.length < 6) {
                alert('Password must be at least 6 characters long.');
                e.preventDefault();
                return;
            }
        });
    });
});