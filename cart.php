<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shipping.css">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="product.css">
</head>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    require "connection.php"; // Ensure this file connects to your database
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['id']);
if (!$isLoggedIn) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit(); // Ensure that the script stops execution after the redirect
}
// Check if product ID is provided
$productId = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch user details if logged in
if (isset($_SESSION['id'])) {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if product is already in cart, if product id is provided.
    if ($productId && isset($_POST['add_to_cart'])) {
        $cartstmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $cartstmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $cartstmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $cartstmt->execute();

        // Add to cart if not already present
        if ($cartstmt->rowCount() == 0) {
            $insertStmt = $pdo->prepare("
                INSERT INTO cart (
                    user_id, 
                    product_id, 
                    AddedToCart
                ) VALUES (
                    :user_id, 
                    :product_id, 
                    1
                )
            ");
            $insertStmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
            $insertStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            
            if ($insertStmt->execute()) {
                $cartMessage = "Product added to cart.";
            } else {
                $cartMessage = "Failed to add product to cart.";
            }
        }
    }

    // Fetch cart items for the logged-in user with discount information
    $cartItemsStmt = $pdo->prepare("
        SELECT p.*, pa.Discount 
        FROM cart c
        JOIN product p ON c.product_id = p.id
        LEFT JOIN productsattributes pa ON p.id = pa.product_id
        WHERE c.user_id = :user_id AND c.AddedToCart = 1
    ");
    $cartItemsStmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $cartItemsStmt->execute();
    $cartItems = $cartItemsStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch product details with discount information
if ($productId) {
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

// Fetch products in cart with discount information
if(isset($_SESSION['id'])){
    $productsStmt = $pdo->prepare("
        SELECT p.*, pa.Discount 
        FROM cart c
        JOIN product p ON c.product_id = p.id
        LEFT JOIN productsattributes pa ON p.id = pa.product_id
        WHERE c.user_id = :user_id AND c.AddedToCart = 1
    ");
    $productsStmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $productsStmt->execute();
    $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
}


?>

<body>
    <?php require "nav.php"; ?>
    <style>

      .cart-total-container {
    background-color: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 10px;
    max-width: 300px;
    margin-left: auto;
    margin-right: auto;
}

.cart-total-container h3 {
    color: #333;
    font-size: 1.2rem;
    border-bottom: 2px solid red;
    padding-bottom: 10px;
    margin-bottom: 15px;
    text-align: center;
}

.cart-total-price {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #ffffff;
    border-radius: 8px;
    padding: 10px 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.cart-total-price span:first-child {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.9rem;
    margin-right: 15px;
}

.cart-total-price span:last-child {
    font-size: 1.2rem;
    font-weight: bold;
    color: red;
    text-align: right;
    margin-left: auto;
}

/* Responsive Design */
@media (max-width: 768px) {
    .cart-total-container {
        max-width: 90%;
        margin: 20px auto;
        padding: 10px;
    }

    .cart-total-container h3 {
        font-size: 1.1rem;
    }

    .cart-total-price {
        flex-direction: row;
        justify-content: space-between;
    }

    .cart-total-price span {
        margin: 0;
    }
}

/* Hover Effect */
.cart-total-container {
    transition: transform 0.3s ease;
}


/* Empty Cart Styling */
.empty-cart {
    text-align: center;
    padding: 30px;
    background-color: #f8f9fa;
    border-radius: 10px;
}

.empty-cart i {
    color: #6c757d;
    margin-bottom: 15px;
    font-size: 50px;
}

.empty-cart p {
    color: #6c757d;
    font-size: 1.2rem;
}
    </style>
 <?php
if(isset($_SESSION['id'])) {
    $totalPriceStmt = $pdo->prepare("
        SELECT SUM(
            p.price * (1 - COALESCE(pa.Discount, 0)/100)
        ) AS total_price
        FROM cart c
        JOIN product p ON c.product_id = p.id
        LEFT JOIN productsattributes pa ON p.id = pa.product_id
        WHERE c.user_id = :user_id
        AND c.AddedToCart = 1
    ");
    $totalPriceStmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $totalPriceStmt->execute();
    $totalPriceResult = $totalPriceStmt->fetch(PDO::FETCH_ASSOC);

    // Store the total price as a numeric value
    $totalCartPrice = $totalPriceResult['total_price'] ?? 0;
}
?>


<!-- HTML for cart total price display -->
<?php if ($isLoggedIn): ?>
<div class="cart-total-container">
    <h3>Cart Total</h3>
    <div class="cart-total-price">
        <span>Total Price:</span>
        <span id="total-cart-price"><?php echo number_format($totalCartPrice, 2); ?>$</span>
        
    </div>
</div>
<?php endif; ?>

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
                        
                       
                        <form action="wishlist.php?id=<?php echo htmlspecialchars($featuredProduct['id']); ?>" method="POST">
    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($featuredProduct['id']); ?>">
    <input type="hidden" name="add_to_wishlist" value="1">
    <button type="submit" class="icon wishlist-icon" title="Add to Favorites" style="border: none;">
        <i class="fas fa-heart"></i>
    </button>
</form>
                        <form action="delete_from_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $featuredProduct['id']; ?>">
                            <button type="submit" class="icon delete-icon" title="Remove from Cart" style="border: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        <form action="buyproduct.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $featuredProduct['id']; ?>">
                            <button type="submit" class="icon cart-icon" title="Buy product" style="border: none;">
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

<?php if (empty($products)): ?>
    <div class="cart-container">
        <div class="cart-header">
            <h2>Your Shopping Cart</h2>
            <span id="cart-count">0 Items</span>
        </div>

        <div id="cart-items" class="cart-items">
           
            <div class="empty-cart">
                <i class="fas fa-shopping-cart" style="font-size: 50px;"></i>
                <p>Your cart is currently empty</p>
            </div>
        </div>

       
    </div>
    <?php endif;?>
   
   
    <?php require "footer.php"; ?>

    <script src="nav.js"></script>
    <script src="index.js"></script>
    <script src="product.js"></script>
</body>
</html>