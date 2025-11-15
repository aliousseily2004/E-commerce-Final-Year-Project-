<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Home</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="nav.css">
    
  
    <link rel="stylesheet" href="footer.css">
    
</head>
<body>
 <?php
 require "nav.php";
 require "connection.php"; 
 ?>

    <div class="shop-container">

    <?php
$query = "
    SELECT DISTINCT p.*, pa.*, r.average_rating 
    FROM product p
    LEFT JOIN productsattributes pa ON p.id = pa.product_id
    LEFT JOIN rating r ON p.id = r.product_id
    WHERE 1=1
    LIMIT 8;
";

$params = [];

// Handle category filter

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

  
} catch (PDOException $e) {
    // Comprehensive error logging
    error_log("Database Error: " . $e->getMessage());
    error_log("Query: " . $query);
    error_log("Params: " . print_r($params, true));
    
    $products = []; 
}


?>
    
   <section class="home">
    <div class="coverimage">
    <img src="cover.webp" alt="Cover Image"> 
    
    <div class="image-text">
        <p class="p1">  Unique clothes for special occasions</p>
        <p class="p2">Dress to Impress: Elevate Your Everyday Look!</p>
        <a href="product.php">
            <button class="button-1">Shop Now!</button>
        </a>

    </div>
    
    </div>
    <div class="categorietext">
        <h2>FASHION COLLECTIONS</h2>
    <h1>FEATURED CATEGORIES</h1>
    <h4>Newest trends from top brands</h4>

    </div>
    
    <section class="categories">
        <div class="categorie1">
            <div class="kid">
                <img src="kid.png" alt="">
                <a href="product.php?category=Kids">
                    <button class="overlay-button">Shop KID</button>
                </a>
            </div>
            <div class="accessories">
                <img src="accessories.png" alt="">
                <a href="product.php?category=Accessories">
                    <button class="overlay-button">Shop ACCESSORIES</button>
                </a>
            </div>
        </div>
        <div class="categorie2">
            <div class="women">
                <img src="women.png" alt="">
                <a href="product.php?category=Women">
                    <button class="overlay-button">Shop WOMEN</button>
                </a>
            </div>
        </div>
        <div class="categorie3">
            <div class="men">
                <img src="men.png" alt="">
                <a href="product.php?category=Men">
                    <button class="overlay-button">Shop MEN</button>
                </a>
            </div>
        </div>
        
    </section>
    <h3>Featured Products</h3>
    <section class="Featured">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <?php
                    $original_price = $product['price'] ?? 0;
                    $discount = $product['Discount'] ?? 0;
                    $discounted_price = $original_price;
                    
                    if ($discount > 0) {
                        $discounted_price = $original_price * (1 - ($discount / 100));
                    }
                    ?>
                <div class="Featured-item">
                    <?php 
                    // Construct the correct web-accessible path to the image
                    $imagePath = 'uploads/' . htmlspecialchars($product['photo1']); 
                    ?>
                  
                    <!-- Link to product details with product ID -->
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <img src="<?php echo $imagePath; ?>" 
                             alt="<?php echo htmlspecialchars($product['category']); ?>">
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
                            <div class="icon" title="Preview" data-target="product<?php echo $product['id']; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <i class="fas fa-eye"></i>
                            </div>
                        
                      
                            <form action="wishlist.php?id=<?php echo $product['product_id']; ?>" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="add_to_wishlist" value="1">
                                <button type="submit" class="icon wishlist-icon" title="Add to Favorites" style="border: none;">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </form>
                            <form action="cart.php?id=<?php echo $product['product_id']; ?>" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="add_to_cart" value="1">
                                <button type="submit" class="icon cart-icon" title="Add to Cart" style="border: none;">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found matching your criteria.</p>
        <?php endif; ?>
    </section>
    
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-container" id="product<?php echo $product['id']; ?>">
                <div class="product-image">
                    <?php 
                    // Use the product data we already have
                    $images = [
                        'photo1' => $product['photo1'] ?? '',
                        'photo2' => $product['photo2'] ?? '',
                        'photo3' => $product['photo3'] ?? '',
                        'photo4' => $product['photo4'] ?? ''
                    ];
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
    data-image="/Final/uploads/<?php echo htmlspecialchars($images[$column]); ?>"
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


<?php

 


$productId = $product['product_id'];
$sql = "SELECT * FROM rating WHERE product_id = :product_id AND user_id = :user_id";

try {
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id',$_SESSION['id'], PDO::PARAM_INT);
   
    
    $stmt->execute();
    
    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
   
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<div class="product-rating">
    <h3>Product Rating:</h3>
    <div class="stars">
    <?php
// Check if there are any results
if (!empty($results)) {
    // Get the first row (since we're checking one product/user combination)
    $ratingData = $results[0];
    
    $userRating = isset($ratingData['rating']) ? $ratingData['rating'] : 0;
    $averageRating = isset($ratingData['average_rating']) ? $ratingData['average_rating'] : 0;
    $ratingCount = isset($ratingData['rating_count']) ? $ratingData['rating_count'] : 0;
    ?>
    
    <div class="rating-stars">
        <?php
        // Display user rating stars
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $userRating) {
                echo '<span class="star filled">★</span>'; // Filled star for user rating
            } else {
                echo '<span class="star">☆</span>'; // Empty star for user rating
            }
        }
        ?>
    </div>
    
    <?php if ($isLoggedIn): ?>
        <p>Your Rating: <?php echo htmlspecialchars($userRating); ?> out of 5</p>
    <?php endif; ?>
    
    <p>Average Rating: <?php echo htmlspecialchars(number_format($averageRating, 1)); ?> out of 5 (<?php echo htmlspecialchars($ratingCount); ?> ratings)</p>
    
    <?php
   
} else {
    // No rating found for this product/user
    ?>
    <div class="rating-stars">
        <?php
        // Display empty stars
        for ($i = 1; $i <= 5; $i++) {
            echo '<span class="star">☆</span>';
        }
        ?>
    </div>
    <?php if ($isLoggedIn): ?>
        <p>You haven't rated this product yet</p>
    <?php endif; ?>
    <p>Average Rating: 0.0 out of 5 (0 ratings)</p>
    <?php
}
?>
    
    <?php if ($isLoggedIn): // Show rating form only if user is logged in ?>
        <!-- Rating Form -->
        <form action="actions/rateproductaction.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            
            <label for="userRating">Rate this product:</label>
            <select name="userRating" id="userRating" required>
                <option value="" disabled selected>Select your rating</option>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>

            </select>
           
            <button type="submit" class="sub-but">Submit Rating</button>
        </form>
    <?php else: ?>
        <p>Please <a href="login.php">log in</a> to rate this product.</p>
    <?php endif; ?>
</div>
</div>

                    <div class="product-options">
                       
                  

<?php if (empty($products)): ?>
    <p class="size-error">Size options not currently available</p>
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
</div>

   
   
    


    <!-- <h3 >Our Quality Promise</h3> -->
    <section class="quality">
        <div class="quality-features">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div class="feature-description">
                    <p>Free Shipping</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div class="feature-description">
                    <p>24/7 Customer Support</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="feature-description">
                    <p>Secure Payment</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-medal"></i>
                </div>
                <div class="feature-description">
                    <p>Good Quality</p>
                </div>
            </div>
        </div>
        
    </section>
   <?php
   require "footer.php";

   ?>
   <script src="nav.js"></script>
   <script src="index.js"></script>
</body>
</html>
