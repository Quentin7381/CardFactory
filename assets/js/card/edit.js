document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.querySelector('input.card_image'); // Select the file input
    const label = document.querySelector('label.card_image'); // Select the label associated with the input

    if (fileInput && label) {
        fileInput.addEventListener('change', function () {
            const file = fileInput.files[0]; // Get the uploaded file
            if (file) {
                const reader = new FileReader(); // Create a FileReader to read the file

                reader.onload = function (e) {
                    // Set the label's background to the uploaded image
                    label.style.backgroundImage = `url(${e.target.result})`;
                    label.style.backgroundSize = 'cover';
                    label.style.backgroundPosition = 'center';
                    label.style.backgroundRepeat = 'no-repeat';

                    // Hide the label's text
                    label.textContent = '';
                };

                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                // Reset the label if no file is selected
                label.style.backgroundImage = '';
                label.textContent = 'Upload Image';
            }
        });
    }
});