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
        saveBtn.on("click",()=>form.trigger("submit"));
        $("#main-container").html(html);
    },
    edit: () => { },
    remove: () => { }

}
