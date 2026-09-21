import Swal from "sweetalert2";

import { removeStudentWarnings, studentHandlers } from "../handlers/student";
import { courseHandlers } from "../handlers/course";
import { administratorHandlers } from "../handlers/administrator";

function showWarning(action) {
    return Swal.fire({
        title: `Do you want to continue to ${action}?`,
        text: `If you continue to ${action}, all changes will be discarded.`,
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: "Continue",
        cancelButtonText: "Discard changes",
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
            removeStudentWarnings();
            studentHandlers.add();
        }
    });
}

export function warningAddCourse(e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    showWarning("add a new course").then(result => {
        if (result.isConfirmed) {
            removeStudentWarnings();
            courseHandlers.add();
        }
    });
}

export function warningAdministrator(e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    showWarning("add a new administrator").then(result => {
        if (result.isConfirmed) {
            administratorHandlers.add();
        }
    });
}
