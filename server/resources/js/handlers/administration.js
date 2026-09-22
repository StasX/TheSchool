import { warningAddAdministrator } from "../messages/warnings";
import { administratorHandlers } from "./administrator";

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
        .off('click', resetAdministrationHandlers)
}

export function removeAdministrationWarningsHandler() {
    $("#add-administrator").off("click", warningAddAdministrator);
}
