import template from '../templates/pages/school.html?raw';
import { courseHandlers } from './courseHandlers';
import {studentRender} from "./renders/student";


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
