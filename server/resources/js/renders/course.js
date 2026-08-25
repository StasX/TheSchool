import { courseHandlers } from '../handlers/course';
import courseListItemTemplate from "../../templates/partials/courseListItem.html?raw";
import courseInfoTemplate from "../../templates/partials/courseInfo.html?raw";
import courseMemberTemplate from "../../templates/partials/courseMember.html?raw";

export function courseRender(data) {
    $("#courses-container").html("");
    $.each(data, (i, course) => {
        const html = $(courseListItemTemplate);
        html.find(".course-name").text(course.Name);
        html.find(".course-description").text(course.Description);
        html.find(".course-img").attr({ "src": course.Image, "alt": course.Name });
        html.on("click", () => courseHandlers.info(course.Course_ID));
        $("#courses-container").append(html);
    });
}

export function courseInfoRender(data) {
    const html = $(courseInfoTemplate);
    html.find("#course-img").attr({ "src": data.Image, "alt": data.Image });
    html.find("#course-name").text(`${data.Name}, ${data.students.length} Students`);
    html.find("#course-description").text(data.Description);
    const studentsContainer = html.find("#members-list");
    $.each(data.students, (i, student) => {
        const member = $(courseMemberTemplate);
        member.find(".member-img").attr({ "alt": student.Name, "src": student.Image });
        member.find(".member-name").text(student.Name)
        studentsContainer.append(member);
    });
    const editBtn = html.find("#edit");
    editBtn.on("click", () => courseHandlers.edit(data));
    $("#main-container").html(html);
}


