import template from "../../templates/partials/administrator.html?raw";
import { display } from "../utils/image";
import { administratorInfoRender, administratorRender } from "../renders/administrator";
import Swal from "sweetalert2";

export const administratorHandlers = {
    add: () => {
        const html = $(template);
        const saveBtn = html.find("#save-administrator");
        const form = html.filter("#administrator-form");
        const fileInput = html.find("#image-file");
        const imageElement = html.find("#image-upload");
                const roleInput = html.find("#role");
        const roles = ['manager', 'sales'];
        $.each(roles, (i, role) => {
            const option = $('<option></option>');
            option.val(role);
            option.text(role);
            roleInput.append(option);
        });
        fileInput.on("change", function () { display(imageElement, this); });
        form.on("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            if (fileInput[0].files.length) {
                formData.set("Image", fileInput[0].files[0]);
            }
            $.ajax({
                method: "POST",
                url: `/api/administrator`,
                data: formData,
                processData: false,
                contentType: false
            }).done((data) => {
                administratorHandlers.info(data.Administrator_ID);
                $.get('/api/administrator').done(administrator => administratorRender(administrator));
            }).fail(xhr => console.error(xhr));
        });
        saveBtn.on("click", () => form.trigger("submit"));
        $("#main-container").html(html);
    },
    edit: administrator => {
        const html = $(template);
        const titleContainer = html.find("#container-title");
        const nameInput = html.find("#name");
        const phoneInput = html.find("#phone");
        const emailInput = html.find("#email");
        const fileInput = html.find("#image-file");
        const imageElement = html.find("#image-upload");
        const buttons = html.filter("#btn-row");
        const saveBtn = buttons.find("#save-administrator");
        const form = html.filter("#administrators-form");
        titleContainer.text("Edit Administrator");
        const btnContainer = $('<div class="col d-flex align-items-center"></div>');
        const removeBtn = $(`
            <button type="button" class="btn btn-sm btn-dark ms-auto">
                Delete <i class="fa-regular fa-trash-can"></i>
            </button>
        `);
        const roleInput = html.find("#role");
        const roles = ['owner', 'manager', 'sales', () => roleInput.val(administrator.Role)];
        if (administrator.Role == 'owner') {
            roles.splice(1, 2);
        }
        $.each(roles, (i, role) => {

            if (typeof role != 'string') {
                role();
                return;
            }
            const option = $('<option></option>');
            option.val(role);
            option.text(role);
            roleInput.append(option);
        });


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
                url: `/api/administrator/${administrator.Administrator_ID}`,
                data: formData,
                processData: false,
                contentType: false
            }).done((data) => {
                administratorHandlers.info(data.Administrator_ID);
                $.get('/api/administrator').done(administrators => administratorRender(administrators));
            }).fail(xhr => console.error(xhr));
        });
        saveBtn.on("click", () => form.trigger("submit"));
        if (administrator.Role != 'owner') {
            removeBtn.on("click", () => administratorHandlers.remove(administrator));
            btnContainer.append(removeBtn);
        }
        buttons.append(btnContainer);
        nameInput.val(administrator.Name);
        phoneInput.val(administrator.Phone);
        emailInput.val(administrator.Email);
        imageElement.attr("src", administrator.Image);
        $("#main-container").html(html);
    },
    remove: administrator => {
        Swal.fire({
            title: `Do you really want to delete administrator: ${administrator.Name}?`,
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
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "DELETE",
                            url: `/api/administrator/${administrator.Administrator_ID}`
                        }).done(() => {
                            Swal.fire({
                                title: "Administrator deleted successfully!",
                                icon: "success",
                            }).then(() => {
                                $.get("/api/administrator").done(administrator => administratorRender(administrator));
                                $("#main-container").html("");
                            });
                        }).fail(xhr => console.error(xhr));
                    }
                });
            }
        });
    }
}
