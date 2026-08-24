import template from "../../templates/partials/course.html?raw";
import { courseRender, courseInfoRender } from "../renders/course";
import { display } from "../utils/image";

export const courseHandlers = {
    info: (id) => {
        $.get(`/api/course/${id}`).done((data) => {
            courseInfoRender(data);
        });
    },
    add: () => {
        const html = $(template);
        const total = html.find("#total");
        const saveBtn = html.find("#save-course");
        const form = html.filter("#courses-form");
        const fileInput = html.find("#image-file");
        const imageElement = html.find("#image-upload");
        total.text(0);
        fileInput.on("change", function () { display(imageElement, this); });
        form.on("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            if (fileInput[0].files.length) {
                formData.set("Image", fileInput[0].files[0]);
            }
            $.ajax({
                method: "POST",
                url: "/api/course",
                data: formData,
                processData: false,
                contentType: false
            }).done((data) => {
                courseHandlers.info(data.Course_ID);
                $.get('/api/course').done((courses) => courseRender(courses));
            }).fail((xhr) => console.error(xhr));
        });
        saveBtn.on("click", () => form.trigger("submit"));
        $("#main-container").html(html);
    },
    edit: (course) => {
        console.log(course)
        const html = $(template);
        const total = html.find("#total");
        const titleContainer = html.find("#container-title");
        const nameInput = html.find("#name");
        const descriptionInput = html.find("#description");
        const saveBtn = html.find("#save-course");
        const removeBtn = html.find("#delete-course");
        const form = html.filter("#courses-form");
        titleContainer.text("Edit Course");
        nameInput.val(course.Name);
        descriptionInput.val(course.Description);
        const fileInput = html.find("#image-file");
        const imageElement = html.find("#image-upload");
        total.text(course.students.length);
        imageElement.attr("src", course.Image);
        fileInput.on("change", function () { display(imageElement, this); });
        removeBtn.on("click", () => courseHandlers.remove(course));
        form.on("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.set("_method", "PUT");
            // if (fileInput[0].files.length) {
            //     formData.set("Image", fileInput[0].files[0]);
            // }
            $.ajax({
                method: "POST",
                url: `/api/course/${course.Course_ID}`,
                data: formData,
                processData: false,
                contentType: false
            }).done((data) => {
                courseHandlers.info(data.Course_ID);
                $.get('/api/course').done((courses) => courseRender(courses));
            }).fail((xhr) => console.error(xhr));
        });
        saveBtn.on("click", () => form.trigger("submit"));
        $("#main-container").html(html);

    },
    remove: (course) => {
        Swal.fire({
            title: `Do you really want to delete course: ${course.Name}?`,
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
                            url: `/api/course/${course.Course_ID}`
                        }).done(() => {
                            Swal.fire({
                                title: "Course deleted successfully!",
                                icon: "success",
                            }).then(() => {
                                $.get("/api/course").done((courses) => courseRender(courses));
                                $("#main-container").html("");
                            });
                        }).fail((xhr) => {
                            console.error(xhr);
                        });
                    }
                });
            }
        });
    }
}
