export function courseValidationConfig({
    edit = false,
} = {}) {
    return {
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            description: {
                required: true
            },
            image: {
                required: !edit,
                extension: "jpg|jpeg|png|gif",
                filesize: 1024 * 1024,
                imagesize: {
                    width: 350,
                    height: 350
                }
            }
        },
        messages: {
            name: {
                required: "Name is required.",
                minlength: "Name must contain at least 2 characters."
            },
            description: {
                required: "Description is required."
            },
            image: {
                required: "Image is required.",
                extension: "Image have to be jpg, png, gif file.",
                filesize: "Image must not exceed 500 KB.",
                imagesize: "Image too large."
            }
        }
    }
}
