
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Home</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   
  
  
    <link rel="stylesheet" href="nav.css">

   

</head>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it hasn't been started
    require "connection.php";

}

$isLoggedIn = isset($_SESSION['id']);
?>


<section class="navbar">
    <div class="nav">
    <div class="nav-logo">
                <img src="logo.png" alt="logo">
            </div>
            <div class="hamburger" onclick="toggleMenu()">
    <i class="fas fa-bars"></i> <!-- Font Awesome bars icon -->
</div>
        <ul class="nav-links">

            <li><a href="index.php">HOME</a></li>
            <li><a href="about.php">ABOUT</a></li>


                    <li class="Men">
                        <a href="product.php?category=Men">MEN</a>
                        <div class="Men-drop">
                        <ul>
                            <li><a href="product.php?category=Men&subcategory=Jeans">Jeans</a></li>
                            <li><a href="product.php?category=Men&subcategory=T-Shirts">T-Shirts</a></li>
                            <li><a href="product.php?category=Men&subcategory=Jackets">Jackets</a></li>
                            <li><a href="product.php?category=Men&subcategory=Hoodies">Hoodies</a></li>
                            <li><a href="product.php?category=Men&subcategory=Shoes">Shoes</a></li>
                        </ul>
                        </div>
                    </li>
                    <li class="women">
                        <a href="product.php?category=Women">WOMEN</a>
                        <div class="women-drop">
                        <ul>
                            <li><a href="product.php?category=Women&subcategory=Jeans">Jeans</a></li>
                            <li><a href="product.php?category=Women&subcategory=T-Shirts">T-Shirts</a></li>
                            <li><a href="product.php?category=Women&subcategory=Jackets">Jackets</a></li>
                            <li><a href="product.php?category=Women&subcategory=Hoodies">Hoodies</a></li>
                            <li><a href="product.php?category=Women&subcategory=Shoes">Shoes</a></li>
                        </ul>
                        </div>
                    </li>
                    <li class="kid">
                        <a href="product.php?category=Kids">KIDS</a>
                        <div class="kid-drop">
                        <ul>
                            <li><a href="product.php?category=Kids&subcategory=Jeans">Jeans</a></li>
                            <li><a href="product.php?category=Kids&subcategory=T-Shirts">T-Shirts</a></li>
                            <li><a href="product.php?category=Kids&subcategory=Jackets">Jackets</a></li>
                            <li><a href="product.php?category=Kids&subcategory=Hoodies">Hoodies</a></li>
                            <li><a href="product.php?category=Kids&subcategory=Shoes">Shoes</a></li>
                        </ul>
                        </div>
                    </li>
                    <li class="Accessories">
                        <a href="product.php?category=Accessories">ACCESSORIES</a>
                        <div class="accessories-drop">
                        <ul>
                            <li><a href="product.php?category=Accessories&subcategory=Bags">Bags</a></li>
                            <li><a href="product.php?category=Accessories&subcategory=Hats">Hats</a></li>
                            <li><a href="product.php?category=Accessories&subcategory=Watches">Watches</a></li>
                            <li><a href="product.php?category=Accessories&subcategory=Sunglasses">Sunglasses</a></li>
                        </ul>
                        </div>
                    </li>
                </ul>
    </div>

        <div class="nav-icons">
    <i class="fas fa-search" id="search-icon"></i>

    <form action="wishlist.php" method="POST" style="display: inline;">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="add_to_wishlist" value="1">
        <button type="submit" id="heart-icon" style="border: none; background: none;">
            <i class="fas fa-heart"></i>
        </button>
    </form>

    <a href="account.php">
        <i class="fas fa-user" id="user-icon"></i>
    </a>

    <form action="cart.php" method="POST" style="display: inline;">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="add_to_cart" value="1">
        <button type="submit" id="cart-icon" style="border: none; background: none;">
            <i class="fas fa-shopping-cart"></i>
        </button>
    </form>

    <?php if ($isLoggedIn): ?>
        <a href="logout.php" id="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    <?php endif; ?>
</div>
    </section>

    <div id="search-overlay" class="search-overlay">
    <div class="search-container">
        <div class="search-header">
            <h2>Search for Products</h2>
            <i class="fas fa-times close-search"></i>
        </div>
        <div class="search-input-container">
            <form method="GET" action="search.php">
                <input type="text" name="search" placeholder="Search for a product" class="search-input" required>
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <div id="search-results-container"></div>
    </div>
</div>
<script>
    function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
}
document.addEventListener('DOMContentLoaded', function() {
    const search = document.getElementById('search-icon');
    const wishlist = document.getElementById('heart-icon');
    const loginAndRegister = document.getElementById('user-icon');
    const cart = document.getElementById('cart-icon');
    const menuToggle = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const menLink = document.querySelector('.Men > a');
    const womenLink = document.querySelector('.women > a');
    const kidLink = document.querySelector('.kid > a');
    const accessoriesLink = document.querySelector('.Accessories > a');
    const menDrop = document.querySelector('.Men-drop');
    const womenDrop = document.querySelector('.women-drop');
    const kidDrop = document.querySelector('.kid-drop');
    const accessoriesDrop = document.querySelector('.accessories-drop');
    function toggleSubMenu(subMenu) {
        subMenu.style.display = subMenu.style.display === 'block' ? 'none' : 'block';
    }

    menLink.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default link behavior
        toggleSubMenu(menDrop);
    });

    womenLink.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSubMenu(womenDrop);
    });

    kidLink.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSubMenu(kidDrop);
    });

    accessoriesLink.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSubMenu(accessoriesDrop);
    });
})
</script>

 <script src="nav.js"></script>
