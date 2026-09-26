export function imagesizeRegister(){
            $.validator.addMethod(
            "imagesize",
            function (value, element, dimensions) {
                if (this.optional(element)) {
                    return true;
                }
                const width = $(element).data("image-width");
                const height = $(element).data("image-height");
                if (width === undefined || height === undefined) {
                    return false;
                }
                return (
                    width <= dimensions.width &&
                    height <= dimensions.height
                );
            },
            "Image dimensions are too large."
        );
}
