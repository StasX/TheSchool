import './bootstrap';
import schoolTemplate from '../templates/school.html?raw';
import administrationTemplate from '../templates/administration.html?raw';
import notFoundTemplate from '../templates/404.html?raw';

$(document).ready(function () {
    let user;
    $(window).on('hashchange', function () {
        switch (location.hash) {
            case '#!school': {
                $('body').html(schoolTemplate);
                break;
            }
            case '#!administration': {
                $('body').html(administrationTemplate);
                break;
            }
            default: {
                if (location.hash) {
                    $('body').html(notFoundTemplate);
                }
            }
        }
    });
    if (!location.hash) {
        $('#login').on('submit', function (e) {
            e.preventDefault();
            const data ={
                Email: $('#user').val(),
                Password: $('#password').val()
            };
            $.post('/api/login', data, function (data) {
                user = data.Administrator;
                location.hash = '#!school';
            }).fail(function (xhr) {
                const error =xhr.status===401 ? 'Invalid username or password' : 'An error occurred. Please try again later.';
                $('#alerts').html(`<div class="alert alert-danger" role="alert">${error}</div>`);
            });
        });
    }
});
