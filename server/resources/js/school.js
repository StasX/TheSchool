import template from '../templates/pages/school.html?raw';
import { studentHandlers } from './studentHandlers';
import { courseHandlers } from './courseHandlers';


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

        $.each(data, (i, student) => {
            const html = $(`
            <div class="row item-row">
                <div class="col-sm-10">
                    <div class="container">
                        <div class="row">${student.Name}</div>
                        <div class="row">${student.Phone}</div>
                    </div>
                </div>
                <div class="col-sm-2">
                <img src="${student.Image}" alt="${student.Name}" />
                </div>
            </div>`);
            html.click(() => studentHandlers.info(student.Student_ID));
            $("#students-container").append(html);
        });
    });
    $.get('/api/course').done((data) => {
        $.each(data, (i, course) => {
            const html = $(`
            <div class="row item-row">
                <div class="col-sm-10">
                    <div class="container">
                        <div class="row">${course.Name}</div>
                        <div class="row">${course.Description}</div>
                    </div>
                </div>
                <div class="col-sm-2">
                <img src="${course.Image}" alt="${course.Name}" />
                </div>
            </div>
            `);
            $("#courses-container").append(html);
        });
    });

}
