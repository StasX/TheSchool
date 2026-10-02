function showAlert(message){
$('#alerts').html(`<div class="alert alert-danger" role="alert">${message}</div>`);
}

export function invalidCredentials(wrongCredentials) {
    const error = wrongCredentials
        ? 'Invalid username or password'
        : 'An error occurred. Please try again later.';
        showAlert(error);
}

export function invalidFields(validator) {
    const errors = validator.errorList.map(error => error.message).join('<br/>');
showAlert(errors);
}

