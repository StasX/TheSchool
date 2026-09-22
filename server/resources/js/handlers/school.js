import { warningAddCourse, warningAddStudent } from "../messages/warnings";
import { courseHandlers } from "./course";
import { studentHandlers } from "./student";

export function resetSchoolHandlers() {
    $("#add-student")
        .off("click", warningAddStudent)
        .on('click', studentHandlers.add)
    $("#add-course")
        .off("click", warningAddCourse)
        .on('click', courseHandlers.add);
}

export function setSchoolWarningsHandler() {
    $("#add-student")
        .off('click', studentHandlers.add)
        .off("click", warningAddStudent)
        .on("click", warningAddStudent);
    $("#add-course")
        .off('click', courseHandlers.add)
        .off("click", warningAddCourse)
        .on("click", warningAddCourse);
    $('#courses-container .item-row')
        .off('click', resetSchoolHandlers)
        .on('click', resetSchoolHandlers);
    $('#students-container .item-row')
        .off('click', resetSchoolHandlers)
        .on('click', resetSchoolHandlers);
}

export function removeSchoolWarningsHandler() {
    $("#add-student").off("click", warningAddStudent);
    $("#add-course").off("click", warningAddCourse);
}
