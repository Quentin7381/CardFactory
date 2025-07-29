document.addEventListener('DOMContentLoaded', function () {
    
    // ----- IMAGE UPLOAD ----- //
    
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

    if(fileInput.dataset.image) {
        let imagePath = fileInput.dataset.image;
        // Append initial '/' if missing
        if (imagePath.charAt(0) !== '/') {
            imagePath = '/' + imagePath;
        }
        console.log(imagePath);
        // If there's a pre-existing image, set it as the label's background
        label.style.backgroundImage = `url(${imagePath})`;
        label.style.backgroundSize = 'cover';
        label.style.backgroundPosition = 'center';
        label.style.backgroundRepeat = 'no-repeat';
        label.textContent = ''; // Hide the label's text
    }

    // ----- THEME CHANGE ----- //

    let themeSelect = document.querySelector('select.template-select');
    let card = document.querySelector('.card');

    if(themeSelect && card) {
        themeSelect.addEventListener('change', function () {
            const selectedOption = themeSelect.selectedOptions[0];
            const cssClass = selectedOption.dataset.cssClass;
            console.log('Selected CSS Class:', cssClass);
        
            card.classList.forEach(className => {
                if (className.startsWith('card-style-')) {
                    card.classList.remove(className);
                }
            });
    
            // Add the new card-style class
            if (cssClass) {
                card.classList.add(`card-style-${cssClass}`);
            }

            // Remove eventual image to force a new upload
            const imageInput = document.querySelector('input.card_image');
            if (imageInput) {
                imageInput.value = ''; // Clear the file input
                label.style.backgroundImage = ''; // Reset the label's background image
                label.textContent = 'Upload Image'; // Reset the label text
            }
        });
    } else {
        console.error("Couldn't retrieve theme selector. Auto theme update disabled.");
    }
});