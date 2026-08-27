import template from "../../templates/partials/course.html?raw";
import { courseRender, courseInfoRender } from "../renders/course";
import { display } from "../utils/image";

export const courseHandlers = {
    info: id => {
        $.get(`/api/course/${id}`).done(data => {
            courseInfoRender(data);
        });
    },
    add: () => {
        const html = $(template);
        const form = html.filter("#courses-form");
        const fileInput = html.find("#image-file");
        const imageElement = html.find("#image-upload");
        html.find("#total").text(0);
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
            }).done(data => {
                courseHandlers.info(data.Course_ID);
                $.get('/api/course').done(courses => courseRender(courses));
            }).fail(xhr => console.error(xhr));
        });
        html.find("#save-course").on("click", () => form.trigger("submit"));
        $("#main-container").html(html);
    },
    edit: course => {
        const html = $(template);
        const form = html.filter("#courses-form");
        html.find("#container-title").text("Edit Course");
        html.find("#name").val(course.Name);
        html.find("#description").val(course.Description);
        const imageElement = html.find("#image-upload");
        html.find("#total").text(course.students.length);
        imageElement.attr("src", course.Image);
        html.find("#image-file").on("change", function () { display(imageElement, this); });
        html.find("#delete-course").on("click", () => courseHandlers.remove(course));
        form.on("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.set("_method", "PUT");
            $.ajax({
                method: "POST",
                url: `/api/course/${course.Course_ID}`,
                data: formData,
                processData: false,
                contentType: false
            }).done(data => {
                courseHandlers.info(data.Course_ID);
                $.get('/api/course').done(courses => courseRender(courses));
            }).fail(xhr => console.error(xhr));
        });
        html.find("#save-course").on("click", () => form.trigger("submit"));
        $("#main-container").html(html);
    },
    remove: course => {
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
        }).then(result => {
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
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "DELETE",
                            url: `/api/course/${course.Course_ID}`
                        }).done(() => {
                            Swal.fire({
                                title: "Course deleted successfully!",
                                icon: "success",
                            }).then(() => {
                                $.get("/api/course").done(courses => courseRender(courses));
                                $("#main-container").html("");
                            });
                        }).fail(xhr => console.error(xhr));
                    }
                });
            }
        });
    }
}
