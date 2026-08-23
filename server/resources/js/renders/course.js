import { courseHandlers } from '../handlers/course';

export function courseRender(data) {
    $("#courses-container").html("");
    $.each(data, (i, course) => {
        const html = $(`
            <div class="row item-row">
                <div class="col-sm-10">
                    <div class="container">
                        <div class="row">${course.Name}</div>
                        <div class="row">${course.Description}</div>
                    </div>
                </div>
                <div class="col-sm-2">
                <img src="${course.Image}" alt="${course.Name}" />
                </div>
            </div>
            `);
        html.on("click", () => courseHandlers.info(course.Course_ID));
        $("#courses-container").append(html);
    });
}
export function courseInfoRender(data) {
    const html = $(`
        <div class = "row">
            <div class="col d-flex align-items-center">
                <b>Course</b>
                <button class="btn btn-sm btn-dark ms-auto" id="edit">Edit</button>
            </div>
        </div>
        <div class="row view-row">
            <hr class="col">
        </div>
        <div class="row view-row">
            <div class = "col-sm-4">
                <img src = "${data.Image}" alt = "${data.Image}" width = "250" />
            </div>
            <div class="col-sm-8">
                <div class="container">
                    <h1 class="row">${data.Name}, ${data.students.length} Students</h1>
                    <p class="row">${data.Description}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="container" id="members-list">
        </div>
    </div>
    `);
    const studentsContainer = html.find("#member-of");
    $.each(data.students, (i, student) => {
        studentsContainer.append(`
                    <div class="row view-row">
                        <div class="col-4"><img alt="${student.Name}" src="${student.Image}" width="60" />
                        </div>
                        <h3 class="col-8">${student.Name}</h3>
                    </div>
                `);
    });
    const editBtn = html.find("#edit");
    editBtn.on("click", () => courseHandlers.edit(data));
    $("#main-container").html(html);
}


