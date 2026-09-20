import './bootstrap';
import school from './school';
import administration from './administration';
import notFound from './notFound';
import AuthApi from './api/authApi';


$(function () {
    let user = null;

    function render(data) {
        user = data;
        switch (location.hash) {
            case '#!school':
                school(user);
                break;

            case '#!administration':
                administration(user);
                break;

            default:
                if (location.hash) {
                    notFound(user);
                }
                break;
        }
    }

    $(window).on('hashchange', () => render(user));

    if (!location.hash) {
        $('#login').on('submit', function (e) {
            e.preventDefault();

            const data = {
                email: $('#user').val(),
                password: $('#password').val()
            };
            AuthApi.login(data).done(function (data) {
                user = data.administrator;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': data.token
                    }
                });
                location.hash = '#!school';
            })
                .fail(function (xhr) {
                    const error = xhr.status === 401
                        ? 'Invalid username or password'
                        : 'An error occurred. Please try again later.';

                    $('#alerts').html(`
                        <div class="alert alert-danger" role="alert">
                            ${error}
                        </div>
                    `);
                });
        });
    } else {
        AuthApi.auth().done(function (data) {
                render(data);
            })
            .fail(function () {
                location.hash = '';
                location.reload();
            });
    }
});
