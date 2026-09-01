export function userRender(user) {
    const navbar = $('#navbar');
    const navItems = navbar.find('.nav-item');
    const canAdministrate = ['owner', 'manager'].includes(user.Role);

    if (canAdministrate && navItems.length === 1) {
        navbar.append(`
            <li class="nav-item">
                <a href="#!administration" class="nav-link">
                    Administration
                </a>
            </li>
        `);
    }
    $('#user-info').text(`${user.Name}, ${user.Role}`);
    $('#user-image').attr('src', user.Image);
    $('#logout').on('click', () => {
        $.post('/api/logout')
            .done(() => {
                location.href='/';
            })
            .fail((xhr) => {
                console.error(xhr);
            });
    });
}
