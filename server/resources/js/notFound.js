import template from '../templates/pages/404.html?raw';
import { navbarRender } from './renders/navbar';

export default function notFound(user) {
    $('body').html(template);
    navbarRender(user);
}
