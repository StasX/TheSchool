import template from '../templates/pages/administration.html?raw';

import { administratorRender } from './renders/administrator';
import { administratorHandlers } from './handlers/administrator';
import { userRender } from './renders/user';
import AdministratorApi from './api/administratorApi';

export default function administration(user) {
    if (!['owner', 'manager'].includes(user.role)) {
        location.replace('/#!school');
        return;
    }
    $('body').html(template);
    userRender(user);
    AdministratorApi.getAll().done((data) => {
        administratorRender(data);
        $('#total-administrators').text(data.length);
    })
        .fail((xhr) => {
            console.error(xhr);
        });
    $("#add-administrator").on("click", administratorHandlers.add);
}
