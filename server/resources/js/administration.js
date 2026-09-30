import template from '../templates/pages/administration.html?raw';

import { administratorRender } from './renders/administrator';
import { administratorHandlers } from './handlers/administrator';
import { navbarRender } from './renders/navbar';
import AdministratorApi from './api/administratorApi';

export default function administration(user) {
    if (!['owner', 'manager'].includes(user.role)) {
        location.replace('/#!school');
        return;
    }
    $('body').html(template);
    navbarRender(user);
    AdministratorApi.getAll().done((data) => {
        administratorRender(data);
    })
        .fail((xhr) => {
            console.error(xhr);
        });
    AdministratorApi.getCount().done((data) => {
        $('#total-administrators').text(data.count);
    })
        .fail((xhr) => {
            console.error(xhr);
        });
    $("#add-administrator").on("click", administratorHandlers.add);
}
