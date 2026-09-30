import AuthApi from "../api/authApi";

export function userRender(user) {
    $('#user-info').text(`${user.name}, ${user.role}`);
    $('#user-image').attr('src', user.image);
}

export function navbarRender(user) {
    const navbar = $('#navbar');
    const navItems = navbar.find('.nav-item');
    const canAdministrate = ['owner', 'manager'].includes(user.role);

    if (canAdministrate && navItems.length === 1) {
        navbar.append(`
            <li class="nav-item">
                <a href="#!administration" class="nav-link">
                    Administration
                </a>
            </li>
        `);
    }
    userRender(user);
    $('#logout').on('click', () => {
        AuthApi.logout().done(() => {
            location.href = '/';
        })
            .fail((xhr) => {
                console.error(xhr);
            });
    });
}
