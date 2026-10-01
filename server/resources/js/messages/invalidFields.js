export default function invalidCredentials(wrongCredentials) {
    const error = wrongCredentials
        ? 'Invalid username or password'
        : 'An error occurred. Please try again later.';
    $('#alerts').html(`<div class="alert alert-danger" role="alert">${error}</div>`);
}
