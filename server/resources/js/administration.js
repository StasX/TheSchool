import template from '../templates/pages/administration.html?raw';
export default function administration(user) {
    $('body').html(template);
    userRender(user);
}
