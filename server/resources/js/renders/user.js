export function userRender(user) {
    const navItems=$("#navbar").find(".nav-item");
    if (user && (user.Role == 'owner' || user.Role == 'manager') && navItems.length == 1) {
        $('#navbar').append(`
                            <li class="nav-item">
                                <a href="#!administration" class="nav-link">
                                    Administration
                                </a>
                            </li>
                        `);
    }
    $('#user-info').text(`${user.Name}, ${user.Role}`);
    $('#user-image').attr('src', user.Image);
    $("#logout").on("click", () => $.post("/api/logout").done(() => {
        location.hash = '';
        location.reload();
    }).fail(xhr => console.error(xhr)));
}
