<?php

   if (session_status() == PHP_SESSION_NONE) {
    session_start();
    require "connection.php"; // Ensure this file connects to your database
}
$isLoggedIn = isset($_SESSION['id']);
if (!$isLoggedIn) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit(); // Ensure that the script stops execution after the redirect
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shipping.css">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="wishlist.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="footer.css">
 
</head>
<body>
<?php
require "nav.php";


    
// Check if user is logged in


$productId = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch user details if logged in
if (isset($_SESSION['id']) && $productId && isset($_POST['add_to_wishlist'])) {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if product is already in wishlist
    $wishlistStmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
    $wishlistStmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $wishlistStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $wishlistStmt->execute();

    // Add to wishlist if not already present
    if ($wishlistStmt->rowCount() == 0) {
        $insertStmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, is_wishlisted) VALUES (:user_id, :product_id, 1)");
        $insertStmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $insertStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        
        if ($insertStmt->execute()) {
            $wishlistMessage = "Product added to wishlist.";
        } else {
            $wishlistMessage = "Failed to add product to wishlist.";
        }
    }
}

// Fetch wishlist items for the logged-in user
if(isset($_SESSION['id'])){
    $wishlist_items_query = $pdo->prepare("
        SELECT p.*, pa.Discount 
        FROM wishlist w
        JOIN product p ON w.product_id = p.id
        LEFT JOIN productsattributes pa ON p.id = pa.product_id
        WHERE w.user_id = :user_id AND w.is_wishlisted = 1
    ");
    $wishlist_items_query->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $wishlist_items_query->execute();
    $wishlist_items = $wishlist_items_query->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch product details with Discount from productattributes
if($productId){
    $productStmt = $pdo->prepare("
        SELECT p.*, pa.Discount 
        FROM product p 
        LEFT JOIN productsattributes pa ON p.id = pa.product_id 
        WHERE p.id = :id
    ");
    $productStmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $productStmt->execute();
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch products for featured section where is_wishlisted = 1, including Discount
if(isset($_SESSION['id'])){
    $productsStmt = $pdo->prepare("
        SELECT p.*, pa.Discount 
        FROM product p 
        JOIN wishlist w ON p.id = w.product_id 
        LEFT JOIN productsattributes pa ON p.id = pa.product_id 
        WHERE w.user_id = :user_id AND w.is_wishlisted = 1
    ");
    $productsStmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $productsStmt->execute();
    $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!-- Featured Products Section -->
<section class="Featured">
<?php if (!empty($products)): ?>
    <?php foreach ($products as $featuredProduct): ?>
        <?php
                $original_price = $featuredProduct['price'] ?? 0;
                $discount = $featuredProduct['Discount'] ?? 0;
                $discounted_price = $original_price;
                
                if ($discount > 0) {
                    $discounted_price = $original_price * (1 - ($discount / 100));
                }
                ?>
        <div class="Featured-item">
            <?php 
            $imagePath = 'uploads/' . htmlspecialchars($featuredProduct['photo1']); 
            ?>
            <a href="product.php?id=<?php echo $featuredProduct['id']; ?>">
                <img src="<?php echo $imagePath; ?>" 
                     alt="<?php echo htmlspecialchars($featuredProduct['category']); ?>">
            </a>
            <div class="price">
                <?php if ($discount > 0): ?>
                    <span class="original-price" style="text-decoration: line-through; color: gray; margin-right: 10px;">
                        <?php echo htmlspecialchars((string)$original_price) . '$'; ?>
                    </span>
                    <span class="discounted-price" style="color: red; font-weight: bold;">
                        <?php echo htmlspecialchars((string)number_format($discounted_price, 2)) . '$'; ?>
                    </span>
                <?php else: ?>
                    <?php echo htmlspecialchars((string)$original_price) . '$'; ?>
                <?php endif; ?>
            </div>
        

                <div class="icon-box">
                    <div class="icons">
                        <div class="icon" title="Preview" data-target="product<?php echo $featuredProduct['id']; ?>">
                            <i class="fas fa-eye"></i>
                        </div>
                        <form action="delete_from_wishlist.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo $featuredProduct['id']; ?>">
    <button type="submit" class="icon delete-icon" title="Remove from Wishlist" style="border: none;">
        <i class="fas fa-trash"></i> <!-- Delete icon -->
    </button>
</form>
<form action="cart.php?id=<?php echo $featuredProduct['id']; ?>" method="POST">
    <input type="hidden" name="product_id" value="<?php echo $featuredProduct['id']; ?>">
    <input type="hidden" name="add_to_cart" value="1">
    <button type="submit" class="icon cart-icon" title="Add to Cart" style="border: none;">
        <i class="fas fa-shopping-cart"></i>
    </button>
</form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    
    <?php endif; ?>
</section>
   <!-- Product Details Section -->
<?php if (!empty($products)): ?>
    <?php foreach ($products as $product): ?>
        <div class="product-container" id="product<?php echo $product['id']; ?>">
            <div class="product-image">
                <?php 
                // Fetch all image paths for this specific product
                $stmt = $pdo->prepare("SELECT photo1, photo2, photo3, photo4 FROM product WHERE id = :id");
                $stmt->bindParam(':id', $product['id'], PDO::PARAM_INT);
                $stmt->execute();
                $images = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <img 
                    src="/Final/uploads/<?php echo !empty($images['photo1']) ? htmlspecialchars($images['photo1']) : 'Featurediamages/featured1.jpg'; ?>" 
                    class="main-image" 
                    alt="<?php echo htmlspecialchars($product['title']); ?>"
                >
                <h3>COLORS</h3>
                <div class="color-images">
                    <?php 
                    // Define the column names
                    $columns = ['photo1', 'photo2', 'photo3', 'photo4'];
                    
                    // Iterate through the columns
                    foreach ($columns as $index => $column): 
                        // Check if the image path exists and is not empty
                        if (!empty($images[$column])):
                    ?>
                        <img 
                            src="/Final/uploads/<?php echo htmlspecialchars($images[$column]); ?>" 
                            class="<?php echo $index === 0 ? 'active' : ''; ?>" 
                            alt="Color <?php echo $index + 1; ?>" 
                            data-image="<?php echo htmlspecialchars($images[$column]); ?>"
                        >
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <div class="product-details">
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    
                    <?php
                    $original_price = $product['price'] ?? 0;
                    $discount = $product['Discount'] ?? 0;
                    $discounted_price = $original_price;
                    
                    if ($discount > 0) {
                        $discounted_price = $original_price * (1 - ($discount / 100));
                    }
                    ?>
                    <div class="product-details">
    <?php if ($discount > 0): ?>
        <div class="price-comparison">
            <p class="original-price">Original Price: <span><?php echo htmlspecialchars(number_format($original_price, 2)); ?>$</span></p>
            <p class="discount">Discount: <span><?php echo htmlspecialchars($discount); ?>%</span></p>
            <p class="discounted-price">
                Discounted Price: <span><?php echo htmlspecialchars(number_format($discounted_price, 2)); ?>$</span>
            </p>
        </div>
    <?php else: ?>
        <p class="prices">Price: <span><?php echo htmlspecialchars(number_format($original_price, 2)); ?>$</span></p>
    <?php endif; ?>
</div>
                <h3>Product Details</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Attribute</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($product['Attribute1']); ?></td>
                            <td><?php echo htmlspecialchars($product['Detail1']); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($product['Attribute2']); ?></td>
                            <td><?php echo htmlspecialchars($product['Detail2']); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($product['Attribute3']); ?></td>
                            <td><?php echo htmlspecialchars($product['Detail3']); ?></td>
                        </tr>
                    </tbody>
                </table>
                <h3>ABOUT THIS ITEM</h3>
                <div class="about-item">
                    <ul>
                        <li><?php echo htmlspecialchars($product['About1']); ?></li>
                        <li><?php echo htmlspecialchars($product['About2']); ?></li>
                        <li><?php echo htmlspecialchars($product['About3']); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

    <!-- Wishlist Section -->
    <div class="wishlist-container">
        <?php if (empty($wishlist_items)): ?>
            <div class="wishlist-empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="wishlist-icon">
                    <!-- Background Circle -->
                    <circle cx="100" cy="100" r="90" fill="#F0F4F8" />
                    
                    <!-- Heart Shape -->
                    <path 
                        d="M100 70 
                           Q120 50, 140 60 
                           Q160 70, 150 90 
                           Q140 110, 100 140 
                           Q60 110, 50 90 
                           Q40 70, 60 60 
                           Q80 50, 100 70Z" 
                        fill="#FF6B6B" 
                        stroke="#333" 
                        stroke-width="3"
                    />
                    
                    <!-- Broken Heart Lines -->
                    <line 
                        x1="100" y1="70" 
                        x2="100" y2="140" 
                        stroke="#333" 
                        stroke-width="2" 
                        stroke-dasharray="5,5"
                    />
                    
                    <!-- Tear Drops -->
                    <path 
                        d="M90 150 Q100 170, 110 150" 
                        fill="none" 
                        stroke="#6B7280" 
                        stroke-width="2"
                    />
                    
                    <!-- Shadow Effect -->
                    <ellipse 
                        cx="100" 
                        cy="190" 
                        rx="60" 
                        ry="10" 
                        fill="rgba(0,0,0,0.1)"
                    />
                    
                    <!-- Directional Arrows -->
                    <g stroke="#6B7280" stroke-width="2">
                        <path 
                            d="M50 30 L100 60 L150 30" 
                            fill="none" 
                            stroke-dasharray="4,4"
                        />
                    </g>
                </svg>
                
                <h3 class="wishlist-title">Your Wishlist is Empty</h3>
                <p class="wishlist-description">
                    Discover items you'll love and add them to your wishlist
                </p>
                
                <div class="wishlist-actions">
                    <a href="/final/product.php" class="wishlist-explore-btn">
                        Start Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            
        <?php endif; ?>
    </div>

    <?php require "footer.php"; ?>

    <script src="nav.js"></script>
    <script src="index.js"></script>
    <script src="product.js"></script>
</body>
</html>