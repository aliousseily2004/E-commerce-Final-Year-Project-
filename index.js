document.addEventListener('DOMContentLoaded', function() {
    // Select all preview icons
    const previewIcons = document.querySelectorAll('.icon[data-target]');
    
    // Select all product containers
    const productContainers = document.querySelectorAll('.product-container');

    // Initially hide all product containers
    productContainers.forEach(container => {
        container.style.display = 'none';
    });

    // Add click event listener to preview icons
    previewIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            // Get the target product container ID
            const targetId = this.getAttribute('data-target');
            const targetContainer = document.getElementById(targetId);

            // Toggle visibility of product containers
            productContainers.forEach(container => {
                if (container.id === targetId) {
                    // Show the product container
                    container.style.display = 'flex';

                    // Disable background scroll
                    document.body.style.overflow = 'hidden';

                    // Create and append the close button
                    const closeButton = document.createElement('button');
                    closeButton.className = 'product-close';
                    closeButton.innerHTML = '&times;'; // Close icon
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
                    closeButton.style.transition = 'background 0.3s ease';

                    // Add hover effect
                    closeButton.addEventListener('mouseover', function() {
                        closeButton.style.background = 'darkred';
                    });
                    closeButton.addEventListener('mouseout', function() {
                        closeButton.style.background = 'red';
                    });

                    // Append the close button to the product container
                    container.appendChild(closeButton);

                    // Add click event to close the product container
                    closeButton.addEventListener('click', function() {
                        container.style.display = 'none';
                        // Re-enable background scroll
                        document.body.style.overflow = 'auto';
                    });
                } else {
                    container.style.display = 'none';
                }
            });
        });
    });

    // Color Image Selection
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
                
                // Update main image
                mainImage.src = e.target.getAttribute('data-image');
            }
        });
    });
});
console.log("Script is running"); // Check if the script is executing

const previewIcons = document.querySelectorAll('.icon[data-target]');
console.log("Number of preview icons:", previewIcons.length); // Check if the correct number of icons are selected

previewIcons.forEach(icon => {
    console.log("Icon data-target:", icon.dataset.target); // Check the data-target value
    // ... rest of your code
});
