export function display(image, input) {
    const file = input.files[0];
    if (file) {
        const imageUrl = URL.createObjectURL(file);
        image.attr('src', imageUrl);
    }
}
