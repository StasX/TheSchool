export function invalidCredentials(wrongCredentials) {
    const error = wrongCredentials
        ? 'Invalid username or password'
        : 'An error occurred. Please try again later.';
    $('#alerts').html(`<div class="alert alert-danger" role="alert">${error}</div>`);
}

export function invalidCourse(validator) {
    const errors = validator.errorList.map(error => error.message).join('<br/>');
$('#alerts').html(`<div class="alert alert-danger" role="alert">${errors}</div>`);
}
