$(function () {
    $('#registerForm').on('keypress', function (e) {
        if(e.which === 13) {
            $('#confirmButton').trigger('click');
        }
    });

    $('#confirmButton').on('click', function() {
        if (! $('#inputEmail').val().match(/[a-z][a-z0-9]{3,}/))
        {
            error('login should be at least 4 characters long and should begin with letter');
            return;
        }

        let password = $('#inputPassword').val();
        if (! password) {
            error('password should contain at least 1 symbol');
            return;
        }
        if (password !== $('#inputPasswordRepeat').val()) {
            error('passwords does not match');
            return;
        }

        $('#registerForm').submit();
    });
});
