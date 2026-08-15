import './bootstrap';
import schoolTemplate from '../templates/pages/school.html?raw';
import administrationTemplate from '../templates/pages/administration.html?raw';
import notFoundTemplate from '../templates/pages/404.html?raw';

$(document).ready(function () {
    let user = null;

    function render() {
        switch (location.hash) {
            case '#!school':
                $('body').html(schoolTemplate);

                console.log('User:', user);

                if (user && (user.Role === 'owner' || user.Role === 'manager')) {
                    $('#navbar').append(`
                        <li class="nav-item">
                            <a href="#!administration" class="nav-link">
                                Administration
                            </a>
                        </li>
                    `);
                    $('#user-info').text(`${user.Name}, ${user.Role}`);
                    $.get(user.Image).done(()=>{
                        $('#user-image').attr('src', user.Image);
                    }).fail(()=>{
                        $('#user-image').attr('src', '/img/user.png');
                    });
                }

                break;

            case '#!administration':
                $('body').html(administrationTemplate);
                break;

            default:
                if (location.hash) {
                    $('body').html(notFoundTemplate);
                }
                break;
        }
    }

    $(window).on('hashchange', render);

    if (!location.hash) {
        $('#login').on('submit', function (e) {
            e.preventDefault();

            const data = {
                Email: $('#user').val(),
                Password: $('#password').val()
            };

            $.post('/api/login', data)
                .done(function (data) {
                    console.log(data)
                    user = data.administrator;
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
        $.get('/api/auth')
            .done(function (data) {
                user = data.administrator;
                render();
            })
            .fail(function () {
                location.hash = '';
                location.reload();
            });
    }
});
