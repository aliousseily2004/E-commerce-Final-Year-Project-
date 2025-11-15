<div class="product-options">
                       
                       <?php
   // First check if any size is available
   $hasSizes = false;
   
   // Check standard sizes
   if ((isset($product['Small']) && $product['Small'] == 1) ||
       (isset($product['Medium']) && $product['Medium'] == 1) ||
       (isset($product['Large']) && $product['Large'] == 1) ||
       (isset($product['ExtraLarge']) && $product['ExtraLarge'] == 1)) {
         
       $hasSizes = true;
       
   }
   
   // Check numeric sizes if no standard sizes found
   if (!$hasSizes) {
       $numericSizes = [24, 25, 26, 27, 36, 37, 38, 39, 40, 41, 42, 43];
       foreach ($numericSizes as $size) {
           if (isset($product[$size]) && $product[$size] === 1) {
               $hasSizes = true;
               break;
           }
       }
   }
   
   // Only show the size select if sizes are available
   if ($hasSizes): ?>
   <div class="product-options">
       <label for="size">Select Size:</label>
       <select name="size" id="size" required>
           <option value="" disabled selected>Select Size</option>
           <?php if (isset($product['Small']) && $product['Small'] == 1): ?>
               <option value="Small">Small</option>
           <?php endif; ?>
           <?php if (isset($product['Medium']) && $product['Medium'] == 1): ?>
               <option value="Medium">Medium</option>
           <?php endif; ?>
           <?php if (isset($product['Large']) && $product['Large'] == 1): ?>
               <option value="Large">Large</option>
           <?php endif; ?>
           <?php if (isset($product['ExtraLarge']) && $product['ExtraLarge'] == 1): ?>
               <option value="ExtraLarge">Extra Large</option>
           <?php endif; ?>
           <?php
           $sizes = [24, 25, 26, 27, 36, 37, 38, 39, 40, 41, 42, 43];
           foreach ($sizes as $size) {
               if (isset($product[$size]) && $product[$size] === 1) {
                   echo '<option value="' . htmlspecialchars($size) . '">' . htmlspecialchars($size) . '</option>';
               }
           }
           ?>
       </select>
   </div>
   <?php endif; ?>

   
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Home</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="nav.css">
    
  
    <link rel="stylesheet" href="footer.css">
    
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
            <div class="hamburger">

        <button class="navbar-toggler" type="button" >
      <span class="navbar-toggler-icon"></span>
    </button></div>
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
        </ul>

    </div>

       <div class="nav-icons">
    <i class="fas fa-search" id="search-icon"></i>

    <!-- Wishlist Form -->
    <form action="wishlist.php" method="POST" style="display: inline;">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="add_to_wishlist" value="1">
        <button type="submit" id="heart-icon" style="border: none; background: none;">
            <i class="fas fa-heart"></i>
        </button>
    </form>

    <!-- Account Link -->
    <a href="account.php">
        <i class="fas fa-user" id="user-icon"></i>
    </a>

    <!-- Cart Form -->
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
        <!-- Search results will be dynamically inserted here -->
        <div id="search-results-container"></div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.querySelector('.navbar-toggler');
    const navLinks = document.querySelector('.nav-links');
    const navCategories = document.querySelectorAll('.nav-links > li');

    // Hamburger toggle
    hamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        if (window.innerWidth <= 769) {
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');
        }
    });

    // Category dropdown toggle
    navCategories.forEach(category => {
        const categoryLink = category.querySelector('a');
        const categoryDropdown = category.querySelector('div');

        if (categoryDropdown) {
            categoryLink.addEventListener('click', (e) => {
                if (window.innerWidth <= 769) {
                    e.preventDefault();
                    const wasActive = category.classList.contains('active');
                    
                    // Toggle current category
                    category.classList.toggle('active');
                    
                    // Close other categories
                    navCategories.forEach(cat => {
                        if (cat !== category) cat.classList.remove('active');
                    });
                    
                    // If closing the same category, ensure it stays closed
                    if (wasActive) category.classList.remove('active');
                }
            });
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 769) {
            if (!navLinks.contains(e.target) && !hamburger.contains(e.target)) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
                navCategories.forEach(cat => cat.classList.remove('active'));
            }
        }
    });

    // Reset on resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 769) {
            navLinks.classList.remove('active');
            hamburger.classList.remove('active');
            navCategories.forEach(cat => cat.classList.remove('active'));
        }
    });
});
</script>


 <script src="nav.js"></script>
