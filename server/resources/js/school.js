import template from '../templates/pages/school.html?raw';
import { courseHandlers } from './handlers/course';
import { studentRender } from "./renders/student";
import { courseRender } from "./renders/course";
import { studentHandlers } from './handlers/student';
import { userRender } from './renders/user';

export default function school(user) {
    $('body').html(template);
    userRender(user);
    $.get('/api/student').done((data) => {
        studentRender(data);
    });
    $("#add-student").on("click", studentHandlers.add);
    $.get('/api/course').done((data) => {
        courseRender(data);
    });
    $("#add-course").on("click", courseHandlers.add);

}
