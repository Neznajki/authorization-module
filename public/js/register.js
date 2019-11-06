$(function () {
    let $inputEmail = $('#inputEmail');

    let registration = new Registration(
        $inputEmail,
        $('#inputPassword'),
        $('#inputPasswordRepeat')
    );
    let emailValidationTimerId;

    $('#registerForm').on('keypress', function (e) {
        if(e.which === 13) {
            $('#confirmButton').trigger('click');
        }
    });

    $inputEmail.on('keypress', function () {
        registration.clearErrors();

        if (registration.isEmailValid()) {
            return;
        }

        emailValidationTimerId = setTimeout(() => {
            // let $inputEmail = $(this);
            emailValidationTimerId = null;
        }, 500);

    });

    $('#confirmButton').on('click', function() {
        let promise = new Promise(registration.validate);

        promise.then(function () {
            $('#registerForm').submit();
        }).catch(function (reason) {
            error(reason.error);
        });

    });
});
