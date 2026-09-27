export function display(image, input) {
    const file = input.files[0];
    const imageElement = image[0];

    if (!file) {
        imageElement.onload = null;
        imageElement.onerror = null;

        $(input).removeData("image-width image-height");
        image.attr("src", "/upload/nope");

        return;
    }

    const imageUrl = URL.createObjectURL(file);

    imageElement.onload = function () {
        $(input).data({
            "image-width": this.naturalWidth,
            "image-height": this.naturalHeight
        });

        URL.revokeObjectURL(imageUrl);

        $(input).valid();
    };

    $(input).removeData("image-width image-height");

    imageElement.onerror = function () {
        URL.revokeObjectURL(imageUrl);

        if (input.files[0] !== file) return;

        $(input).removeData("image-width image-height");
        $(input).valid();
    };

    image.attr("src", imageUrl);
}
