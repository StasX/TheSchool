import { studentHandlers } from '../handlers/student';
import studentListItemTemplate from "../../templates/partials/studentListItem.html?raw";
import studentInfoTemplate from "../../templates/partials/studentInfo.html?raw";
import memberItemTemplate from "../../templates/partials/memberOf.html?raw";

export function studentRender(data) {
    $("#students-container").html('');
    $.each(data, (i, student) => {
        const html = $(studentListItemTemplate);
        html.find(".student-name").text(student.Name);
        html.find(".student-phone").text(student.Phone);
        html.find(".student-img").attr({ "src": student.Image, "alt": student.Name });
        html.on("click", () => studentHandlers.info(student.Student_ID));
        $("#students-container").append(html);
    });
}

export function studentInfoRender(data) {
    const html = $(studentInfoTemplate);
    html.find("#student-img").attr({ "src": data.Image, "alt": data.Name });
    html.find("#student-name").text(data.Name);
    html.find("student-phone").text(data.Phone);
    html.find("#student-email").text(data.Email);
    const coursesElement = html.find("#member-of");
    $.each(data.courses, (i, course) => {
        const memberItem = $(memberItemTemplate);
        memberItem.find(".course-name").text(course.Name);
        memberItem.find(".course-img").attr({ "alt": course.Name, "src": course.Image });
        coursesElement.append(memberItem);
    });
    html.find("#edit").on("click", () => studentHandlers.edit(data));
    $("#main-container").html(html);
}

