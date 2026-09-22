import { warningAddAdministrator } from "../messages/warnings";
import { administratorHandlers } from "./administrator";
import { courseHandlers } from "./course";
import { studentHandlers } from "./student";

export function resetAdministrationHandlers() {
    $("#add-administrator")
        .off("click", warningAddAdministrator)
        .on('click', administratorHandlers.add);
}

export function setAdministrationWarningsHandler() {
    $("#add-administrator")
        .off('click', administratorHandlers.add)
        .off("click", warningAddAdministrator)
        .on("click", warningAddAdministrator);

    $('#administrators-container .item-row')
        .off('click', resetAdministratorHandlers)
        .on('click', resetSchoolHandlers);
}

export function removeAdministrationWarningsHandler() {
    $("#add-administrator").off("click", warningAddStudent);
}
