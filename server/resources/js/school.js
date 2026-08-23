import template from '../templates/pages/school.html?raw';
import { courseHandlers } from './handlers/course';
import { studentRender } from "./renders/student";
import { courseRender } from "./renders/course";
import { studentHandlers } from './handlers/student';


export default function school(user) {
    $('body').html(template);

    if (user && (user.Role === 'owner' || user.Role === 'manager')) {
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
    $.get('/api/student').done((data) => {
        studentRender(data);
    });
    $("#add-student").on("click", studentHandlers.add);
    $.get('/api/course').done((data) => {
        courseRender(data);
    });
    $("#add-course").on("click", courseHandlers.add);

}
