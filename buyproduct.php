<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "connection.php"; // Ensure this file contains your database connection logic

$isLoggedIn = isset($_SESSION['id']);
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$possibleSizes = ['Small', 'Medium', 'Large', 'ExtraLarge', '36', '37', '38', '39', 
                 '24', '25', '26', '27', '40', '41', '42', '43'];
$availableSizes = [];

// Handle saving product details to session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy'])) {
    if (!isset($_SESSION['id'])) {
        die('User not logged in');
    }

    $userId = $_SESSION['id'];
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $size = $_POST['size'] ?? 'N/A';
    $unitPrice = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $totalPrice = $unitPrice * $quantity;
    $photo = $_POST['active_image'];

    try {
        $pdo->beginTransaction();

        // Insert order
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                product_id, user_id, quantity, size, photo, order_date, price
            ) VALUES (
                :product_id, :user_id, :quantity, :size, :photo, NOW(), :price
            )
        ");
        
        $stmt->execute([
            ':product_id' => $productId,
            ':user_id'    => $userId,
            ':quantity'   => $quantity,
            ':size'      => $size,
            ':photo'     => $photo,
            ':price'     => $totalPrice
        ]);

        // Update product stock
        $updateStmt = $pdo->prepare("
            UPDATE product 
            SET stock = stock - :quantity 
            WHERE id = :product_id
        ");
        $updateStmt->execute([
            ':quantity'   => $quantity,
            ':product_id' => $productId
        ]);

        $pdo->commit();
        

    } catch (PDOException $e) {
        $pdo->rollBack();
        die('Order failed: ' . $e->getMessage());
    }
}




if (isset($productId)) {
    try {
        // Modified SQL query to join with product attributes
        $stmt = $pdo->prepare("
            SELECT p.*, pa.* 
            FROM product p
            LEFT JOIN productsattributes pa ON p.id = pa.product_id
            WHERE p.id = :productid
        ");
        $stmt->bindParam(':productid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check available sizes
        if ($products) {
            foreach ($possibleSizes as $size) {
                if (isset($products[$size]) && $products[$size] == 1) {
                    $availableSizes[] = $size;
                }
            }
        }
        
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}



?>
<style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #f8f9fa;
    --accent-color: #e67e22;
    --text-dark: #2c3e50;
    --text-light: #ffffff;
}

.product-container {
    max-width: 1000px;
    margin: 1.5rem auto;
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    background: var(--secondary-color);
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.product-images {
    display: grid;
    gap: 1rem;
}

.main-image img {
    width: 100%;
    height: 400px;
    object-fit: contain;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 5px;
    background: white;
    transition: transform 0.3s ease;
}

.thumbnails {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.8rem;
}

.thumbnails img {
    width: 100%;
    height: 80px;
    object-fit: contain;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    padding: 3px;
    background: white;
}

.thumbnails img:hover {
    transform: scale(1.03);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.product-details {
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.product-title {
    font-size: 1.8rem;
    color: var(--primary-color);
    margin-bottom: 0.75rem;
    line-height: 1.2;
}

.product-category {
    color: #6c757d;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.product-price {
    font-size: 1.5rem;
    color: var(--accent-color);
    margin: 1rem 0;
    font-weight: 600;
}

.stock-status {
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 4px;
    display: inline-block;
    margin: 0.5rem 0;
    font-size: 0.9rem;
}

.product-description {
    margin: 1.25rem 0;
    padding: 1rem 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.product-description h3 {
    font-size: 1.2rem;
    color: var(--primary-color);
    margin-bottom: 0.75rem;
}

.product-description ul {
    padding-left: 1.2rem;
    line-height: 1.6;
}

.detail-section {
    margin: 1.25rem 0;
}

.detail-item {
    display: grid;
    grid-template-columns: 100px 1fr;
    margin: 0.5rem 0;
    padding: 0.75rem;
    background-color: rgba(248, 249, 250, 0.8);
    border-radius: 6px;
    font-size: 0.95rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.buy-button {
    background-color: var(--accent-color);
    color: var(--text-light);
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 1rem;
}

.buy-button:hover {
    background-color: #d35400;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}
.quantity-selector {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-top: 1rem;
}

.quantity-selector label {
    font-weight: 500;
    color: var(--primary-color);
}

.quantity-selector select {
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
    font-size: 1rem;
}

.buy-button-wrapper {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-top: 1rem;
}
.total-price {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-left: auto;
}
.size-selector {
    margin: 1rem 0;
}

.size-selector label {
    display: block;
    font-weight: 500;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.size-selector select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
    font-size: 1rem;
}
@media (max-width: 768px) {
    .product-container {
        grid-template-columns: 1fr;
        padding: 15px;
        gap: 1rem;
    }
    
    .main-image img {
        height: 250px;
    }
    
    .product-title {
        font-size: 1.5rem;
    }
    
    .product-price {
        font-size: 1.3rem;
    }
    
    .thumbnails img {
        height: 60px;
    }
}
</style>
<form method="POST" action="">

<div class="product-container">
<input type="hidden" name="product_id" value="<?= $productId ?>">
    <input type="hidden" name="price" value="<?= $products['price'] ?>">
    <input type="hidden" name="active_image" id="activeImageInput" 
           value="/Final/uploads/<?= htmlspecialchars($products['photo1']) ?>">
    <div class="product-images">
        <!-- Main Image -->
        <div class="main-image">
            <img 
                src="/Final/uploads/<?php echo htmlspecialchars($products['photo1']); ?>" 
                alt="<?php echo htmlspecialchars($products['title']); ?>"
                data-image-url="/Final/uploads/<?php echo htmlspecialchars($products['photo1']); ?>"
            >
        </div>

        <!-- Thumbnails -->
        <div class="thumbnails">
    <?php if (!empty($products['photo1'])): ?>
        <img 
            src="/Final/uploads/<?php echo htmlspecialchars($products['photo1']); ?>" 
            alt="<?php echo htmlspecialchars($products['title']); ?> thumbnail 1"
            data-image-url="/Final/uploads/<?php echo htmlspecialchars($products['photo1']); ?>"
        >
    <?php endif; ?>

    <?php if (!empty($products['photo2'])): ?>
        <img 
            src="/Final/uploads/<?php echo htmlspecialchars($products['photo2']); ?>" 
            alt="<?php echo htmlspecialchars($products['title']); ?> thumbnail 2"
            data-image-url="/Final/uploads/<?php echo htmlspecialchars($products['photo2']); ?>"
        >
    <?php endif; ?>

    <?php if (!empty($products['photo3'])): ?>
        <img 
            src="/Final/uploads/<?php echo htmlspecialchars($products['photo3']); ?>" 
            alt="<?php echo htmlspecialchars($products['title']); ?> thumbnail 3"
            data-image-url="/Final/uploads/<?php echo htmlspecialchars($products['photo3']); ?>"
        >
    <?php endif; ?>

    <?php if (!empty($products['photo4'])): ?>
        <img 
            src="/Final/uploads/<?php echo htmlspecialchars($products['photo4']); ?>" 
            alt="<?php echo htmlspecialchars($products['title']); ?> thumbnail 4"
            data-image-url="/Final/uploads/<?php echo htmlspecialchars($products['photo4']); ?>"
        >
    <?php endif; ?>
</div>
    </div>

    <div class="product-details">
    <h1 class="product-title"><?php echo htmlspecialchars($products['title']); ?></h1>
    <div class="product-category"><?php echo htmlspecialchars($products['category'] . ' / ' . $products['subcategory']); ?></div>
    
    <div class="product-price" data-price="<?php echo $products['price']; ?>">
        $<?php echo number_format($products['price'], 2); ?>
    </div>
    
    <?php if ($products['stock'] == 0): ?>
        <div class="stock-status out-of-stock">
            <span class="status-text">Currently Unavailable</span>
            <p class="stock-message">This product is out of stock. Please check back later.</p>
        </div>
    <?php else: ?>
        <div class="stock-status <?php echo ($products['stock'] > 5 ? 'in-stock' : 'low-stock'); ?>">
            <?php 
            if ($products['stock'] > 5) {
                echo 'In Stock';
            } else {
                echo 'Only ' . $products['stock'] . ' Left - Order Soon!';
            }
            ?>
        </div>
    <?php endif; ?>
    
    <?php if ($products['stock'] > 0 && !empty($availableSizes)): ?>
        <div class="size-selector">
            <label for="size">Select Size:</label>
            <select name="size" id="size" required>
                <option value="">Choose Size</option>
                <?php foreach ($availableSizes as $size): ?>
                    <option value="<?php echo htmlspecialchars($size); ?>">
                        <?php echo htmlspecialchars($size); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>
    <style>
    .stock-status {
        margin: 10px 0;
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
    }
    
    .in-stock {
        background-color: #e6f3e6;
        color: #2a7e19;
    }
    
    .low-stock {
        background-color: #fff4e6;
        color: #ff8c00;
    }
    
    .out-of-stock {
        background-color: #ffe6e6;
        color: #d9534f;
        display: flex;
        flex-direction: column;
    }
    
    .out-of-stock .status-text {
        font-size: 1.2em;
    }
    
    .out-of-stock .stock-message {
        font-size: 0.9em;
        margin-top: 5px;
        color: #666;
    }
    
    .size-selector {
        margin: 15px 0;
    }
    
    .size-selector select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>

<?php if ($products['stock'] > 0): ?>
        <div class="buy-button-wrapper">
            <div class="quantity-selector">
                <label for="quantity">Quantity:</label>
                <select name="quantity" id="quantity">
                    <?php for ($i = 1; $i <= $products['stock']; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <div class="total-price">
                    Total: $<span id="totalAmount"><?php echo number_format($products['price'], 2); ?></span>
                </div>
            </div>
        </div>
        
        <button type="submit" name="buy" class="buy-button" id="buyButton">Buy</button>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantitySelect = document.getElementById('quantity');
    const priceElement = document.querySelector('.product-price');
    const totalAmount = document.getElementById('totalAmount');
    const price = parseFloat(priceElement.dataset.price);
    const mainImage = document.querySelector('.main-image img');
    const thumbnails = document.querySelectorAll('.thumbnails img');
    const sizeSelect = document.getElementById('size');
    const buyButton = document.getElementById('buyButton');

    // Price and Total Calculation
    function updateTotal() {
        const quantity = parseInt(quantitySelect.value);
        const total = (price * quantity).toFixed(2);
        totalAmount.textContent = total;
    }
    quantitySelect.addEventListener('change', updateTotal);
    updateTotal(); // Initial calculation

    // Image swapping functionality
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Swap main image source
            mainImage.src = this.src;
            mainImage.dataset.imageUrl = this.dataset.imageUrl;
            
            // Remove active class from all thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked thumbnail
            this.classList.add('active');
        });
    });

    // Optional: Add hover effect to thumbnails
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        thumbnail.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Buy Button Handler
    buyButton.addEventListener('click', function() {
        // Validate size selection if size selector exists
        if (sizeSelect && sizeSelect.value === '') {
            alert('Please select a size');
            return;
        }

        // Gather product details
        
    });
});
</script>
<script>
// Add this to update the hidden input when images change
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.querySelector('.main-image img');
    const activeImageInput = document.getElementById('activeImageInput');

    // Update hidden input when main image changes
    function updateActiveImage() {
        activeImageInput.value = mainImage.dataset.imageUrl;
    }

    // Initial value
    updateActiveImage();

    // Update on image change
    mainImage.addEventListener('load', updateActiveImage);
});
</script>
<style>
/* Add this CSS to style the active thumbnail */
.thumbnails img.active {
    border: 2px solid var(--accent-color);
    box-shadow: 0 2px 8px rgba(230, 126, 34, 0.3);
    transform: scale(1.05);
}
</style>