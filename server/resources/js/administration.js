import template from '../templates/pages/administration.html?raw';
import { userRender } from './renders/user';
export default function administration(user) {
    $('body').html(template);
    userRender(user);
}
