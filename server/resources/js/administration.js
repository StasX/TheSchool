import template from '../templates/pages/administration.html?raw';
import { administratorRender } from './renders/administrator';
import { userRender } from './renders/user';

export default function administration(user) {
    $('body').html(template);
    userRender(user);
    $.get('/api/administrator').done((data) => {
        administratorRender(data);
    });
}
