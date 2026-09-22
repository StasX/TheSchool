import Swal from "sweetalert2";

import { removeSchoolWarningsHandler, setSchoolWarningsHandler } from "../handlers/school";
import { studentHandlers } from "../handlers/student";
import { courseHandlers } from "../handlers/course";
import { administratorHandlers } from "../handlers/administrator";

function showWarning(action) {
    return Swal.fire({
        title: `Do you want to continue to ${action}?`,
        text: `If you will continue to ${action}, all changes will be discarded.`,
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: "Continue",
        cancelButtonText: "Cancel",
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-dark",
            cancelButton: "btn btn-dark ms-2"
        }
    });
}

export function warningAddStudent(e) {
    e.preventDefault();
    showWarning("add a new student").then(result => {
        if (result.isConfirmed) {
            removeSchoolWarningsHandler();
            studentHandlers.add();
        }
    });
}

export function warningAddCourse(e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    showWarning("add a new course").then(result => {
        if (result.isConfirmed) {
            removeSchoolWarningsHandler();
            courseHandlers.add();
        }
    });
}

export function warningAddAdministrator(e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    showWarning("add a new administrator").then(result => {
        if (result.isConfirmed) {
            administratorHandlers.add();
        }
    });
}
