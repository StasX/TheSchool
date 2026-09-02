import template from '../templates/pages/administration.html?raw';

import { administratorRender } from './renders/administrator';
import { administratorHandlers } from './handlers/administrator';
import { userRender } from './renders/user';

export default function administration(user) {
    if (!['owner', 'manager'].includes(user.Role)) {
        location.replace('/#!school');
        return;
    }
    $('body').html(template);
    userRender(user);
    $.get('/api/administrator')
        .done((data) => {
            administratorRender(data);
        })
        .fail((xhr) => {
            console.error(xhr);
        });
        $("#add-administrator").on("click", administratorHandlers.add);
}
