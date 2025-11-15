document.addEventListener('DOMContentLoaded', function() {
    const search = document.getElementById('search-icon');
    const wishlist = document.getElementById('heart-icon');
    const loginAndRegister = document.getElementById('user-icon');
    const cart = document.getElementById('cart-icon');

    function ShowMessage(icon, message) {
        const messageDiv = document.createElement('div');
        messageDiv.textContent = message;
        messageDiv.className = 'message';
        document.body.appendChild(messageDiv);

        icon.addEventListener('mouseover', function() {
            const rect = icon.getBoundingClientRect();
            messageDiv.style.display = 'block';
            messageDiv.style.left = (rect.left + rect.width / 2 - messageDiv.offsetWidth / 2) + 'px';
            messageDiv.style.top = (rect.bottom + 5) + 'px';
            messageDiv.classList.add('show');
        });

        icon.addEventListener('mouseleave', function() {
            messageDiv.classList.remove('show');
        });
    }

    ShowMessage(search, 'Search');
    ShowMessage(wishlist, 'Wishlist');
    ShowMessage(loginAndRegister, 'My Account');
    ShowMessage(cart, 'Cart');

    // Search functionality
    const searchIcon = document.getElementById('search-icon');
    const searchOverlay = document.getElementById('search-overlay');
    const closeSearchIcon = document.querySelector('.close-search');

    function Search() {
        searchOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Disable scrolling when search overlay is active
    }

    function closeSearch() {
        searchOverlay.classList.remove('active');
        document.body.style.overflow = 'auto'; // Re-enable scrolling when search overlay is closed
    }

    searchIcon.addEventListener('click', Search);
    closeSearchIcon.addEventListener('click', closeSearch);

    // Close overlay when clicking outside the search container
    searchOverlay.addEventListener('click', function(event) {
        if (event.target === searchOverlay) {
            closeSearch();
        }
    });
   
});


