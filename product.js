document.querySelectorAll('.product-nav label').forEach(label => {
    label.addEventListener('click', function() {
        const dropdown = this.nextElementSibling; // Get the dropdown div
        if (dropdown) {
            dropdown.classList.toggle('show'); // Toggle the 'show' class
        }
    });
});
function updatePriceValue(value) {
    document.getElementById('priceValue').textContent = value;
}
document.addEventListener('DOMContentLoaded', function() {
    // Select all preview icons
    const previewIcons = document.querySelectorAll('.icon[title="Preview"]');
    
    // Select the product container
    const productContainers = document.querySelectorAll('.product-container');

    // Detailed logging for debugging
    console.log("Preview Icons:", previewIcons.length);
    console.log("Product Containers:", productContainers.length);

    // Initial hide for all product containers
    productContainers.forEach(container => {
        container.style.display = 'none';
    });

    // Add click event listener to preview icons
    previewIcons.forEach(icon => {
        // Log each icon's details
        console.log("Icon Data Target:", icon.getAttribute('data-target'));

        icon.addEventListener('click', function() {
            // Get the target product container ID
            const targetId = this.getAttribute('data-target');
            console.log("Clicked Target ID:", targetId);

            // Find the corresponding product container
            const targetContainer = document.getElementById(targetId);

            // Enhanced error handling
            if (!targetContainer) {
                console.error(`No product container found for target: ${targetId}`);
                
                // Log all container IDs for troubleshooting
                productContainers.forEach(container => {
                    console.log("Available Container ID:", container.id);
                });
                return;
            }

            // Hide all containers first
            productContainers.forEach(container => {
                container.style.display = 'none';
            });

            // Show the target container
            targetContainer.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Create close button logic (similar to previous implementation)
            const closeButton = document.createElement('button');
            closeButton.className = 'product-close';
            closeButton.innerHTML = '&times;';
            closeButton.style.position = 'absolute';
            closeButton.style.top = '10px';
            closeButton.style.right = '10px';
            closeButton.style.background = 'red';
            closeButton.style.color = 'white';
            closeButton.style.border = 'none';
            closeButton.style.fontSize = '30px';
            closeButton.style.cursor = 'pointer';
            closeButton.style.borderRadius = '50%';
            closeButton.style.width = '40px';
            closeButton.style.height = '40px';
            closeButton.style.display = 'flex';
            closeButton.style.alignItems = 'center';
            closeButton.style.justifyContent = 'center';
            closeButton.style.zIndex = '1000';

            // Remove any existing close buttons
            const existingCloseButton = targetContainer.querySelector('.product-close');
            if (existingCloseButton) {
                existingCloseButton.remove();
            }

            // Append close button
            targetContainer.appendChild(closeButton);

            // Close button functionality
            closeButton.addEventListener('click', function() {
                targetContainer.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        });
    });

    // Color Image Selection (remains the same as previous implementation)
    document.querySelectorAll('.color-images').forEach(colorContainer => {
        colorContainer.addEventListener('click', function(e) {
            if (e.target.tagName === 'IMG') {
                const productImage = e.target.closest('.product-image');
                const mainImage = productImage.querySelector('.main-image');
                
                // Remove active class from all color images
                colorContainer.querySelectorAll('img').forEach(img => {
                    img.classList.remove('active');
                });
                
                // Add active class to clicked image
                e.target.classList.add('active');
                
                // Get the data-image attribute
                const newImageSrc = e.target.getAttribute('data-image');
                
                // Construct full path (adjust the prefix as needed)
                const fullImagePath = `/Final/uploads/${newImageSrc}`;
                
                // Update main image
                mainImage.src = fullImagePath;
                
                // Debugging logs
                console.log('Original data-image:', newImageSrc);
                console.log('Full image path:', fullImagePath);
            }
        });
    });
});