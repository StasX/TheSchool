import template from "../../templates/partials/student.html?raw";
import { display } from "../utils/image";
import { studentInfoRender, studentRender } from "../renders/student";
import Swal from "sweetalert2";

export const studentHandlers = {
    info: (id) => {
        $.get(`/api/student/${id}`).done((data) => {
            studentInfoRender(data);
        }
        ).fail(xhr => console.error(xhr));
    },
    add: () => {
        const html = $(template);
        const saveBtn = html.find("#save-student");
        const form = html.filter("#students-form");
        const fileInput = html.find("#image-file");
        const imageElement = html.find("#image-upload");
        const coursesContainer = form.find("#courses-container");
        fileInput.on("change", function () { display(imageElement, this); });
        form.on("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            if (fileInput[0].files.length) {
                formData.set("Image", fileInput[0].files[0]);
            }
            $.ajax({
                method: "POST",
                url: `/api/student`,
                data: formData,
                processData: false,
                contentType: false
            }).done((data) => {
                studentHandlers.info(data.Student_ID);
                $.get('/api/student').done((students) => studentRender(students));
            }).fail(xhr => console.error(xhr));
        });
        saveBtn.on("click", () => form.trigger("submit"));
        $.get("/api/course").done((data) => $.each(data, (id, course) => {
            const $course = $(`
                <div class="col">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="courses[]" value="${course.Course_ID}" id="course-${course.Course_ID}">
                    <label class="form-check-label" for="course-${course.Course_ID}">${course.Name}</label>
                    </div>
                </div>
            `);
            coursesContainer.append($course);
        }));

        $("#main-container").html(html);
    },
    edit: (student) => {
        const html = $(template);
        const titleContainer = html.find("#container-title");
        const nameInput = html.find("#name");
        const phoneInput = html.find("#phone");
        const emailInput = html.find("#email");
        const fileInput = html.find("#image-file");
        const imageElement = html.find("#image-upload");
        const buttons = html.filter("#btn-row");
        const saveBtn = buttons.find("#save-student");
        const form = html.filter("#students-form");
        const coursesContainer = form.find("#courses-container");
        titleContainer.text("Edit Student");
        const btnContainer = $('<div class="col d-flex align-items-center"></div>');
        const removeBtn = $(`
            <button type="button" class="btn btn-sm btn-dark ms-auto">
                Delete <i class="fa-regular fa-trash-can"></i>
            </button>
        `);
        fileInput.on("change", function () { display(imageElement, this); });
        form.on("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.set("_method", "PUT");
            if (fileInput[0].files.length) {
                formData.set("Image", fileInput[0].files[0]);
            }
            $.ajax({
                method: "POST",
                url: `/api/student/${student.Student_ID}`,
                data: formData,
                processData: false,
                contentType: false
            }).done((data) => {
                studentHandlers.info(data.Student_ID);
                $.get('/api/student').done((students) => studentRender(students));
            }).fail(xhr => console.error(xhr));
        });
        removeBtn.on("click", () => studentHandlers.remove(student));
        saveBtn.on("click", () => form.trigger("submit"));
        btnContainer.append(removeBtn);
        buttons.append(btnContainer);
        nameInput.val(student.Name);
        phoneInput.val(student.Phone);
        emailInput.val(student.Email);
        imageElement.attr("src", student.Image);
        const subscriptions = student.courses.map((obj) => obj.Course_ID);
        $.get("/api/course").done((data) => $.each(data, (id, course) => {
            const $course = $(`
                <div class="col">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="courses[]" value="${course.Course_ID}" id="course-${course.Course_ID}">
                    <label class="form-check-label" for="course-${course.Course_ID}">${course.Name}</label>
                    </div>
                </div>
            `);
            if (subscriptions.includes(course.Course_ID)) {
                $course.find(`#course-${course.Course_ID}`).prop("checked", true);
            }
            coursesContainer.append($course);
        }));
        $("#main-container").html(html);
    },
    remove: (student) => {
        Swal.fire({
            title: `Do you really want to delete student: ${student.Name}?`,
            icon: "question",
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            buttonsStyling: false,

            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-dark ms-2"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Removed data cannot be restored!",
                    text: "Do you want to continue?",
                    icon: "warning",
                    showCloseButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Continue",
                    cancelButtonText: "Abort",
                    buttonsStyling: false,

                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-dark ms-2"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "DELETE",
                            url: `/api/student/${student.Student_ID}`
                        }).done(() => {
                            Swal.fire({
                                title: "Student deleted successfully!",
                                icon: "success",
                            }).then(() => {
                                $.get("/api/student").done((students) => studentRender(students));
                                $("#main-container").html("");
                            });
                        }).fail(xhr => console.error(xhr));
                    }
                });
            }
        });
    }
}
