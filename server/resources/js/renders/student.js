import { studentHandlers } from '../handlers/student';

export function studentRender(data) {
    $("#students-container").html('');
    $.each(data, (i, student) => {
        const html = $(`
            <div class="row item-row">
                <div class="col-sm-10">
                    <div class="container">
                        <div class="row">${student.Name}</div>
                        <div class="row">${student.Phone}</div>
                    </div>
                </div>
                <div class="col-sm-2">
                <img src="${student.Image}" alt="${student.Name}" />
                </div>
            </div>`);
        html.on("click", () => studentHandlers.info(student.Student_ID));
        $("#students-container").append(html);
    });
}
