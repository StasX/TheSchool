import './bootstrap';
import schoolTemplate from '..templates/school.html';
import administrationTemplate from '..templates/administration.html';
import notFoundTemplate from '..templates/administration.html';

$(document).ready(function () {
    $(window).on('hashchange', function () {
        switch (window.location.hash) {
            case '/#!school': {
                $('body').html(schoolTemplate);
                break;
            }
            case '/#!administration': {
                $('body').html(administrationTemplate);
                break;
            }
            default: {
                if (window.location.hash) {
                    $('body').html(notFoundTemplate);
                }
            }
        }

    });
});
