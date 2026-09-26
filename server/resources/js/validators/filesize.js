export function filesizeRegister(){
    $.validator.addMethod(
            "filesize",
            function (value, element, maxSize) {
                if (this.optional(element)) {
                    return true;
                }
                return element.files[0].size <= maxSize;
            },
            "File is too large."
        );
}
