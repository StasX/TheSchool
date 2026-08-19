import template from "../templates/partials/student.html?raw";

export const studentHandlers = {
    info: (id) => {
        $.get(`/api/student/${id}`).done((data) => {
            const html = $(`
                <div class="row view-row">
                    <div class="col d-flex align-items-center">
                        <b>Student</b>
                        <button class="btn btn-sm btn-dark ms-auto" id="edit">Edit</button>
                    </div>
                </div>
                <div class="row view-row">
                    <hr class="col">
                </div>
                <div class="row view-row">
                    <div class="container col-sm-4">
                        <img src="${data.Image}" width="250" alt="${data.Name}" />
                    </div>
                    <div class="col-sm-8">
                        <div class="container">
                            <h1 class="row">${data.Name}</h1>
                            <h4 class="row">${data.Phone}</h4>
                            <h4 class="row">${data.Email}</h4>
                        </div>
                    </div>
                </div>
                <div class="row view-row">
                    <div class="container" id="member-of">
                    </div>
                </div>
            `);
            const coursesElement = html.find("#member-of");
            $.each(data.courses, (i, course) => {
                coursesElement.append(`
                    <div class="row view-row">
                        <div class="col-sm-4"><img alt="${course.Name}" src="${course.Image}" width="60" />
                        </div>
                        <h3 class="col-sm-8 col-xs-8">${course.Name}</h3>
                    </div>
                `);
            });
            const editBtn = html.find("#edit");
            editBtn.on("click", () => studentHandlers.edit(data));
            $("#main-container").html(html);
        }
        ).fail((xhr) => { console.log(xhr) });
    },
    add: () => { },
    edit: (student) => {
        const html = $(template);
        $("#main-container").html(html);
        $("#container-title").text("Edit Student");
        $("#name").val(student.Name);
        $("#phone").val(student.Phone);
        $("#email").val(student.Email);
        $("#image-upload").attr("src", student.Image);
        $("#image-upload").css({ "visibility": "visible" });
    },
    remove: () => { }

}
