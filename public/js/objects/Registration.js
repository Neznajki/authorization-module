Registration = function (
    $inputEmail,
    $inputPassword,
    $inputPasswordRepeat
) {
    this.errorContainers = {
        'inputEmail': $('#incorrectEmailError'),
        'inputPassword': $('#incorrectPasswordError'),
        'inputPasswordRepeat': $('#incorrectPasswordRepeatError'),
    };

    this.serverValidateUrl = '/validate/register/';

    this.isEmailValid = () => {
        let result = $inputEmail.val().match(/[a-z0-9.]+@[a-z0-9]+\.[a-z]+/i);

        if (! result) {
            this.showError('inputEmail')
        }

        return result;
    };

    this.isPasswordValid = () => {
        let password = $inputPassword.val();
        if (! password) {
            this.showError('inputPassword');
            return false;
        }

        if (password !== $inputPasswordRepeat.val()) {
            this.showError('inputPasswordRepeat');
            return false;
        }

        return true;
    };

    this.showError = (container) => {
        if (! this.errorContainers.hasOwnProperty(container)) {
            throw container + ' input does not exists';
        }
        this.errorContainers[container].show();
    };

    this.clearErrors = () => {
        for(let i in this.errorContainers) {
            // noinspection JSUnfilteredForInLoop
            this.errorContainers[i].hide();
        }
    };

    this.validate = (resolve, reject) => {
        let formValid = this.isEmailValid();

        formValid = this.isPasswordValid() && formValid;

        return formValid && this.emailServerValidation(resolve, reject);
    };

    this.emailServerValidation = (resolve, reject) => {
        $.ajax({
            url: this.serverValidateUrl + encodeURIComponent($inputEmail.val()),
            type: "GET",
            dataType: "json",
            success: function (data) {
                resolve(data);
            },
            error: function (responseData) {
                if (responseData.hasOwnProperty('responseJSON') && responseData.responseJSON) {
                    reject(responseData.responseJSON);
                } else {
                    reject({'success': 0, 'error': 'invalid response format'});
                }
            }
        });
    };

    return this;
};
