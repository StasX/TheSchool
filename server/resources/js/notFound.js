import template from '../templates/pages/404.html?raw';
import { userRender } from './renders/user';

export default function notFound(user) {
    $('body').html(template);
    userRender(user);
}
