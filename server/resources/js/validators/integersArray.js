export function integersArrayRegister() {
    $.validator.addMethod(
        "integerArray",
        function (value, element) {
            const values = $(element.form)
                .find('input[name="course[]"]:checked')
                .map(function () {
                    return $(this).val();
                })
                .get();
            return values.every(Number.isInteger);
        },
        "Courses must contain only integer IDs."
    );
}
