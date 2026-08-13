import './bootstrap';
import schoolTemplate from '../templates/school.html?raw';
import administrationTemplate from '../templates/administration.html?raw';
import notFoundTemplate from '../templates/404.html?raw';

$(document).ready(function () {
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

    }
});
