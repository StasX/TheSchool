import administratorListItemTemplate from "../../templates/partials/administratorListItem.html?raw";
import { administratorHandlers } from "../handlers/administrator";

export function administratorRender(data) {
    $("#administrators-container").html("");
    $.each(data, (i, administrator) => {
        const html = $(administratorListItemTemplate);
        html.find(".administrator-name").text(administrator.Name);
        html.find(".administrator-role").text(administrator.Description);
        html.find(".administrator-img").attr({ "src": administrator.Image, "alt": administrator.Name });
        html.on("click", () => administratorHandlers.edit(administrator));
        $("#administrators-container").append(html);
    });
}
