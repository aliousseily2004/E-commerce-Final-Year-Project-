<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="footer.css">
</head>
<body>
<?php
require "nav.php";
require "connection.php";

$productsPerPage = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $productsPerPage;

// Base query with calculated price
$baseQuery = "
    SELECT DISTINCT p.id, p.*, pa.*, r.average_rating,r.rating_count,
    CASE 
        WHEN pa.Discount > 0 THEN p.price * (1 - (pa.Discount / 100))
        ELSE p.price 
    END as calculated_price
    FROM product p
    LEFT JOIN productsattributes pa ON p.id = pa.product_id
    LEFT JOIN rating r ON p.id = r.product_id
    WHERE 1=1
";

// Count query for total products
$countQuery = "
    SELECT COUNT(DISTINCT p.id) as total 
    FROM product p
    LEFT JOIN productsattributes pa ON p.id = pa.product_id
    LEFT JOIN rating r ON p.id = r.product_id
    WHERE 1=1
";

// Initialize parameters array for prepared statement
$params = [];

// Handle category filter
if (isset($_GET['category'])) {
    $category = $_GET['category'];
    $baseQuery .= " AND p.category = :category";
    $countQuery .= " AND p.category = :category";
    $params[':category'] = $category;
}

// Handle subcategory filter
if (isset($_GET['subcategory'])) {
    $subcategory = $_GET['subcategory'];
    $baseQuery .= " AND p.subcategory = :subcategory";
    $countQuery .= " AND p.subcategory = :subcategory";
    $params[':subcategory'] = $subcategory;
}

// Handle rating filter
if (isset($_GET['rating'])) {
    $rating = (int)$_GET['rating'];
    $baseQuery .= " AND r.average_rating >= :rating";
    $countQuery .= " AND r.average_rating >= :rating";
    $params[':rating'] = $rating;
}

// Handle price filter
if (isset($_GET['price'])) {
    $price = filter_var($_GET['price'], FILTER_SANITIZE_NUMBER_INT);
    if ($price !== false) {
        $baseQuery .= " AND p.price <= :price";
        $countQuery .= " AND p.price <= :price";
        $params[':price'] = $price;
    }
}

// Handle discount filter
$discount_filter = isset($_GET['discount']) ? $_GET['discount'] : null;
if ($discount_filter !== null) {
    switch ($discount_filter) {
        case '5-10':
            $baseQuery .= " AND (pa.Discount >= 5 AND pa.Discount < 10)";
            $countQuery .= " AND (pa.Discount >= 5 AND pa.Discount < 10)";
            break;
        case '10-15':
            $baseQuery .= " AND (pa.Discount >= 10 AND pa.Discount < 15)";
            $countQuery .= " AND (pa.Discount >= 10 AND pa.Discount < 15)";
            break;
        case '15-25':
            $baseQuery .= " AND (pa.Discount >= 15 AND pa.Discount < 25)";
            $countQuery .= " AND (pa.Discount >= 15 AND pa.Discount < 25)";
            break;
        case '25+':
            $baseQuery .= " AND (pa.Discount >= 25)";
            $countQuery .= " AND (pa.Discount >= 25)";
            break;
        case 'no-discount':
            $baseQuery .= " AND (pa.Discount IS NULL OR pa.Discount = 0)";
            $countQuery .= " AND (pa.Discount IS NULL OR pa.Discount = 0)";
            break;
    }
}

// Handle size filter
$selectedSizes = isset($_GET['sizes']) ? $_GET['sizes'] : [];
if (!empty($selectedSizes)) {
    $sizeConditions = [];
    foreach ($selectedSizes as $size) {
        switch(strtolower($size)) {
            case 'small':
                $sizeConditions[] = "pa.small = 1";
                break;
            case 'medium':
                $sizeConditions[] = "pa.medium = 1";
                break;
            case 'large':
                $sizeConditions[] = "pa.large = 1";
                break;
            case 'extralarge':
                $sizeConditions[] = "pa.extralarge = 1";
                break;
        }
    }
    
    if (!empty($sizeConditions)) {
        $baseQuery .= " AND (" . implode(" OR ", $sizeConditions) . ")";
        $countQuery .= " AND (" . implode(" OR ", $sizeConditions) . ")";
    }
}

// Handle number filter
$selectedNumbers = isset($_GET['numbers']) ? (array)$_GET['numbers'] : [];
if (!empty($selectedNumbers)) {
    $numberConditions = [];
    foreach ($selectedNumbers as $numberRange) {
        switch($numberRange) {
            case '36-39':
                $numberConditions[] = "(pa.`36` = 1 OR pa.`37` = 1 OR pa.`38` = 1 OR pa.`39` = 1)";
                break;
            case '24-27':
                $numberConditions[] = "(pa.`24` = 1 OR pa.`25` = 1 OR pa.`26` = 1 OR pa.`27` = 1)";
                break;
            case '40-43':
                $numberConditions[] = "(pa.`40` = 1 OR pa.`41` = 1 OR pa.`42` = 1 OR pa.`43` = 1)";
                break;
        }
    }
    
    if (!empty($numberConditions)) {
        $baseQuery .= " AND (" . implode(" OR ", $numberConditions) . ")";
        $countQuery .= " AND (" . implode(" OR ", $numberConditions) . ")";
    }
}

// Handle sorting
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : '';
switch ($sortOrder) {
    case 'price_asc':
        $baseQuery .= " ORDER BY 
            CASE 
                WHEN pa.Discount > 0 THEN CAST(p.price * (1 - (pa.Discount / 100)) AS DECIMAL(10,2))
                ELSE CAST(p.price AS DECIMAL(10,2))
            END ASC";
        break;
    case 'price_desc':
        $baseQuery .= " ORDER BY 
            CASE 
                WHEN pa.Discount > 0 THEN CAST(p.price * (1 - (pa.Discount / 100)) AS DECIMAL(10,2))
                ELSE CAST(p.price AS DECIMAL(10,2))
            END DESC";
        break;
    default:
        $baseQuery .= " ORDER BY p.id DESC";
        break;
}

// Prepare pagination query
$paginatedQuery = $baseQuery . " LIMIT :offset, :limit";

try {
    // Get total number of products
    $countStmt = $pdo->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalProducts = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalProducts / $productsPerPage);

    // Get paginated products
    $paginatedStmt = $pdo->prepare($paginatedQuery);
    
    // Bind pagination parameters
    $paginatedStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $paginatedStmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
    
    // Bind existing parameters
    foreach ($params as $key => $value) {
        $paginatedStmt->bindValue($key, $value);
    }
    
    $paginatedStmt->execute();
    $products = $paginatedStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    error_log("Query: " . $baseQuery);
    error_log("Params: " . print_r($params, true));
    
    $products = []; 
    $totalPages = 0;
}
?>
<script>
    function sortByPrice(order) {
    const currentParams = new URLSearchParams(window.location.search);
    
    // Remove existing sort parameter
    currentParams.delete('sort');
    
    // Add new sort parameter
    currentParams.set('sort', order === 'asc' ? 'price_asc' : 'price_desc');
    
    // Remove page parameter to start from first page
    currentParams.delete('page');
    
    // Redirect with new parameters
    window.location.search = currentParams.toString();
    }
</script>

<style>
.price-sort-container {
    margin-bottom: 15px;
}

.price-sort-buttons {
    display: flex;
    gap: 5px;
}

.price-sort-btn {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    background-color: #ff4d4d;
    border: 1px solid #ff1a1a;
    border-radius: 3px;
    cursor: pointer;
    color: white;
    font-size: 0.8em;
    transition: background-color 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.price-sort-btn:hover {
    background-color: #ff3333;
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.price-sort-btn svg {
    width: 16px;
    height: 16px;
    stroke: white;
}

/* Active/Selected state */
.price-sort-btn.active {
    background-color: #cc0000;
    border-color: #990000;
}
</style>
    
    <div class="shop-container">

    

<section class="product-nav">
    
    <div class="product-categories">
    <div class="price-sort-container">
        <h2>Sort by Price</h2>
        <div class="price-sort-buttons">
            <button class="price-sort-btn" onclick="sortByPrice('asc')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 16 4 4 4-4"/>
                    <path d="M7 20V4"/>
                    <path d="M11 12h4"/>
                    <path d="M11 16h7"/>
                    <path d="M11 8h10"/>
                </svg>
                Low to High
            </button>
            <button class="price-sort-btn" onclick="sortByPrice('desc')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 8 4-4 4 4"/>
                    <path d="M7 4v16"/>
                    <path d="M11 12h4"/>
                    <path d="M11 16h7"/>
                    <path d="M11 8h10"/>
                </svg>
                High to Low
            </button>
        </div>
    </div>
        <h2>Categories</h2>
        <ul>
            <li>
            <input type="checkbox" id="all-category" class="nav-checkbox-all" data-href="product.php">
            <label><a href="product.php" style="display: inline;">All </a></label>
        </li>
            <li>
                <input type="checkbox" id="men-category" class="nav-checkbox" data-href="product.php?category=Men">
                <label>Men <span class="arrow category-checkbox"><i class="fa-solid fa-angle-down"></i></span></label>
                <div class="ProductMen-drop">
                    <ul>
                        
                        <li>
                            <input type="checkbox" id="men-jeans" class="nav-checkbox" data-href="product.php?category=Men&subcategory=Jeans">
                            <a href="product.php?category=Men&subcategory=Jeans">Jeans</a>
                        </li>
                        <li>
                            <input type="checkbox" id="men-tshirts" class="nav-checkbox" data-href="product.php?category=Men&subcategory=T-Shirts">
                            <a href="product.php?category=Men&subcategory=T-Shirts">T-Shirts</a>
                        </li>
                        <li>
                            <input type="checkbox" id="men-jackets" class="nav-checkbox" data-href="product.php?category=Men&subcategory=Jackets">
                            <a href="product.php?category=Men&subcategory=Jackets">Jackets</a>
                        </li>
                        <li>
                            <input type="checkbox" id="men-hoodies" class="nav-checkbox" data-href="product.php?category=Men&subcategory=Hoodies">
                            <a href="product.php?category=Men&subcategory=Hoodies">Hoodies</a>
                        </li>
                        <li>
                            <input type="checkbox" id="men-shoes" class="nav-checkbox" data-href="product.php?category=Men&subcategory=Shoes">
                            <a href="product.php?category=Men&subcategory=Shoes">Shoes</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li>
                <input type="checkbox" id="women-category" class="nav-checkbox" data-href="product.php?category=Women">
                <label>Women <span class="arrow category-checkbox"><i class="fa-solid fa-angle-down"></i></span></label>
                <div class="ProductWomen-drop">
                    <ul>
                        <li>
                            <input type="checkbox" id="women-jeans" class="nav-checkbox" data-href="product.php?category=Women&subcategory=Jeans">
                            <a href="product.php?category=Women&subcategory=Jeans">Jeans</a>
                        </li>
                        <li>
                            <input type="checkbox" id="women-tshirts" class="nav-checkbox" data-href="product.php?category=Women&subcategory=T-Shirts">
                            <a href="product.php?category=Women&subcategory=T-Shirts">T-Shirts</a>
                        </li>
                        <li>
                            <input type="checkbox" id="women-jackets" class="nav-checkbox" data-href="product.php?category=Women&subcategory=Jackets">
                            <a href="product.php?category=Women&subcategory=Jackets">Jackets</a>
                        </li>
                        <li>
                            <input type="checkbox" id="women-hoodies" class="nav-checkbox" data-href="product.php?category=Women&subcategory=Hoodies">
                            <a href="product.php?category=Women&subcategory=Hoodies">Hoodies</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li>
                <input type="checkbox" id="kids-category" class="nav-checkbox" data-href="product.php?category=Kids">
                <label>Kids <span class="arrow category-checkbox"><i class="fa-solid fa-angle-down"></i></span></label>
                <div class="Productkid-drop">
                    <ul>
                        <li>
                            <input type="checkbox" id="kids-jeans" class="nav-checkbox" data-href="product.php?category=Kids&subcategory=Jeans">
                            <a href="product.php?category=Kids&subcategory=Jeans">Jeans</a>
                        </li>
                        <li>
                            <input type="checkbox" id="kids-tshirts" class="nav-checkbox" data-href="product.php?category=Kids&subcategory=T-Shirts">
                            <a href="product.php?category=Kids&subcategory=T-Shirts">T-Shirts</a>
                        </li>
                        <li>
                            <input type="checkbox" id="kids-jackets" class="nav-checkbox" data-href="product.php?category=Kids&subcategory=Jackets">
                            <a href="product.php?category=Kids&subcategory=Jackets">Jackets</a>
                        </li>
                        <li>
                            <input type="checkbox" id="kids-hoodies" class="nav-checkbox" data-href="product.php?category=Kids&subcategory=Hoodies">
                            <a href="product.php?category=Kids&subcategory=Hoodies">Hoodies</a>
                        </li>
                        <li>
                            <input type="checkbox" id="kids-shoes" class="nav-checkbox" data-href="product.php?category=Kids&subcategory=Shoes">
                            <a href="product.php?category=Kids&subcategory=Shoes">Shoes</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li>
                <input type="checkbox" id="accessories-category" class="nav-checkbox" data-href="product.php?category=Accessories">
                <label>Accessories <span class="arrow category-checkbox"><i class="fa-solid fa-angle-down"></i></span></label>
                <div class="ProductAccessories-drop">
                    <ul>
                        <li>
                            <input type="checkbox" id="accessories-bags" class="nav-checkbox" data-href="product.php?category=Accessories&subcategory=Bags">
                            <a href="product.php?category=Accessories&subcategory=Bags">Bags</a>
                        </li>
                        <li>
                            <input type="checkbox" id="accessories-hats" class="nav-checkbox" data-href="product.php?category=Accessories&subcategory=Hats">
                            <a href="product.php?category=Accessories&subcategory=Hats">Hats</a>
                        </li>
                        <li>
                            <input type="checkbox" id="accessories-watches" class="nav-checkbox" data-href="product.php?category=Accessories&subcategory=Watches">
                            <a href="product.php?category=Accessories&subcategory=Watches">Watches</a>
                        </li>
                        <li>
                            <input type="checkbox" id="accessories-sunglasses" class="nav-checkbox" data-href="product.php?category=Accessories&subcategory=Sunglasses">
                            <a href="product.php?category=Accessories&subcategory=Sunglasses">Sunglasses</a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    <div class="price-container">
        <div class="price-header">
            <h2>Price Range</h2>
        </div>

        <input 
            type="range" 
            id="priceRange" 
            class="price-range"
            min="1" 
            max="99" 
            value="50" 
            step="1" 
            oninput="updatePriceValue(this.value)"
        >

        <div class="price-labels">
    <button onclick="filterPrice()" class="price-filter-btn"> 
        Filter under <span class="price-value">$<span id="priceValue"><?php echo isset($_GET['price']) ? htmlspecialchars($_GET['price']) : 50; ?></span></span>
    </button>
</div>
    </div>

    <script>
        function updatePriceValue(value) {
            document.getElementById('priceValue').textContent = value;
        }

        function filterPrice() {
            let price = document.getElementById('priceValue').textContent;
            window.location.href = "product.php?price=" + price; // Replace your_target_page.php
        }
    </script>

<div class="rating">
    <h2>Rating</h2>
    <form method="GET" action="">
        <ul>
            <li style="font-size: 16px;">
                <input type="checkbox" id="rating-4" name="rating" value="4"
                    <?php echo (isset($_GET['rating']) && $_GET['rating'] >= 4) ? 'checked' : ''; ?> />
                <label for="rating-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i>
                </label>
                (& Up)
            </li>
        </ul>
        <input type="submit" value="Filter" style="display: none;" id="submit-btn" />
    </form>
</div>

<script>
    // Automatically submit the form when checkbox changes
    document.getElementById('rating-4').addEventListener('change', function() {
        document.getElementById('submit-btn').click();
    });
</script>

    <div class="discount">
        <h2>Discount</h2>
        <ul>
            <li>
                <label>
                    <input type="checkbox" 
                           class="discount-checkbox" 
                           data-value="5-10"
                           <?php echo ($discount_filter === '5-10') ? 'checked' : ''; ?>
                    > 5% - 10%
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox" 
                           class="discount-checkbox" 
                           data-value="10-15"
                           <?php echo ($discount_filter === '10-15') ? 'checked' : ''; ?>
                    > 10% - 15%
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox" 
                           class="discount-checkbox" 
                           data-value="15-25"
                           <?php echo ($discount_filter === '15-25') ? 'checked' : ''; ?>
                    > 15% - 25%
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox" 
                           class="discount-checkbox" 
                           data-value="25+"
                           <?php echo ($discount_filter === '25+') ? 'checked' : ''; ?>
                    > More than 25%
                </label>
            </li>
        </ul>
    </div>

    <script>
        document.querySelectorAll('.discount-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // Uncheck other checkboxes
                document.querySelectorAll('.discount-checkbox').forEach(function(otherCheckbox) {
                    if (otherCheckbox !== checkbox) {
                        otherCheckbox.checked = false;
                    }
                });

                // Get the selected discount value
                const discountValue = this.getAttribute('data-value');
                
                // Construct the URL with the selected discount
                const baseUrl = window.location.pathname;
                const queryParams = new URLSearchParams(window.location.search);

                // If checkbox is checked, set discount parameter
                if (this.checked) {
                    queryParams.set('discount', discountValue);
                } else {
                    queryParams.delete('discount');
                }

                queryParams.delete('page');

                window.location.href = `${baseUrl}?${queryParams.toString()}`;
            });
        });

        // Highlight current filter on page load
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const currentDiscount = urlParams.get('discount');
            
            if (currentDiscount) {
                const checkbox = document.querySelector(`.discount-checkbox[data-value="${currentDiscount}"]`);
                if (checkbox) checkbox.checked = true;
            }
        });
    </script>

<div class="size">
    <h2>Sizes</h2>
    <ul>
        <li>
            <label>
                <input type="checkbox" name="size" value="small" onclick="filterProducts()"> Small
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="size" value="Medium" onclick="filterProducts()"> Medium
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="size" value="large" onclick="filterProducts()"> Large
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="size" value="ExtraLarge" onclick="filterProducts()"> Extra Large
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="numbers[]" value="36-39" onclick="filterProducts()"> 36-39
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="numbers[]" value="24-27" onclick="filterProducts()"> 24-27
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="numbers[]" value="40-43" onclick="filterProducts()"> 40-43
            </label>
        </li>
    </ul>
</div>



</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkboxes with class 'nav-checkbox'
        const checkboxes = document.querySelectorAll('.nav-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('click', function() {
                // Navigate to the URL stored in data-href
                window.location.href = this.getAttribute('data-href');
            });
        });
    });
</script>

    

<!-- Featured Products Section -->
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
                    <a href="product.php?id=<?php echo $product['product_id']; ?>">
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
                            <div class="icon" title="Preview" data-target="product<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
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
    
   
    <!-- Product Details Section -->
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-container" id="product<?php echo $product['product_id']; ?>">
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


<?php

 


$productId = $product['product_id'];
$sql = "SELECT * FROM rating WHERE product_id = :product_id ";

try {
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    
   
    
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

    <p>
    <span id="commentLink-<?php echo $product['product_id']; ?>" 
              style="cursor: pointer; color: blue; text-decoration: underline;">
            Comment
        </span>
    </p>
    
    <div id="commentSection-<?php echo $product['product_id']; ?>" style="display: none;">
        <form action="actions/commentaction.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            <label for="userComment">Your Comment:</label>
            <textarea name="userComment" id="userComment" rows="4" cols="50"></textarea>
            <button type="submit" class="sub-but">Submit Comment</button>
        </form>
    </div>
    <p>
    <a href="Showcomment.php?product_id=<?php echo $product['product_id']; ?>">Show Comments on this product</a>
</p>
    <script>
        document.getElementById('commentLink-<?php echo $product['product_id']; ?>').onclick = function() {
            var commentSection = document.getElementById('commentSection-<?php echo $product['product_id']; ?>');
            commentSection.style.display = commentSection.style.display === 'none' ? 'block' : 'none';
        };
    </script>
    <?php else: ?>
        <p>Please <a href="login.php">log in</a> to rate this product.</p>
        <p>Please <a href="login.php">log in</a> to comment on this product.</p>
        <p>Please <a href="login.php">log in</a> to Show comment on this product.</p>
    
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

<?php if ($totalPages > 1): ?>
        <div class="pagination-container">
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    // Build current URL with page parameter
                    $currentUrl = $_SERVER['PHP_SELF'] . '?';
                    $getParams = $_GET;
                    $getParams['page'] = $i;
                    $paginationUrl = $currentUrl . http_build_query($getParams);
                    ?>
                    <a href="<?php echo $paginationUrl; ?>"
                       class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
<style>
.pagination-container {
    display: flex;
    justify-content: center;
    padding: 20px 0;

    width: 100%;
 
}

.pagination {
    display: flex;
    align-items: center;
    gap: 5px;
}

.pagination a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    color: #333;
    text-decoration: none;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination a.active {
    background-color: #ff4d4d;
    color: white;
    border-color: #ff4d4d;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pagination a:hover:not(.active) {
    background-color: #f0f0f0;
    border-color: #ccc;
}

.pagination a.prev,
.pagination a.next {
    background-color: #f8f9fa;
    color: #333;
    border: 1px solid #ddd;
}

.pagination a.prev:hover,
.pagination a.next:hover {
    background-color: #e9ecef;
}




</style>
<?php require "footer.php"; ?>

<script>
    // Categories Navigation
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.nav-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('click', function(e) {
                e.preventDefault();
                const targetUrl = new URL(this.dataset.href, window.location.origin);
                const targetParams = new URLSearchParams(targetUrl.search);
                const currentParams = new URLSearchParams(window.location.search);

                // Clear existing category/subcategory when changing main category
                if (!targetParams.has('subcategory')) {
                    currentParams.delete('subcategory');
                }

                // Update parameters
                targetParams.forEach((value, key) => currentParams.set(key, value));
                currentParams.delete('page');
                
                window.location.href = `${window.location.pathname}?${currentParams.toString()}`;
            });
        });
    });
     const allCategoryCheckbox = document.querySelector('.nav-checkbox-all');
        if (allCategoryCheckbox) {
            allCategoryCheckbox.addEventListener('click', function(e) {
                e.preventDefault();
                const targetUrl = this.dataset.href;
                window.location.href = targetUrl;
            });
        }
    
    // Price Filter
    function filterPrice() {
        const price = document.getElementById('priceValue').textContent;
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.set('price', price);
        currentParams.delete('page');
        window.location.search = currentParams.toString();
    }

    // Rating Filter
    document.getElementById('rating-4').addEventListener('change', function() {
        const currentParams = new URLSearchParams(window.location.search);
        this.checked ? currentParams.set('rating', 4) : currentParams.delete('rating');
        currentParams.delete('page');
        window.location.search = currentParams.toString();
    });

    // Discount Filter (existing code already handles parameters correctly)
    
    // Size Filter
    function filterProducts() {
        const currentParams = new URLSearchParams(window.location.search);
        
        // Clear existing size parameters
        ['sizes[]', 'numbers[]'].forEach(param => {
            currentParams.delete(param);
        });

        // Add new sizes
        document.querySelectorAll('input[name="size"]:checked').forEach(checkbox => {
            currentParams.append('sizes[]', checkbox.value);
        });

        // Add new numbers
        document.querySelectorAll('input[name="numbers[]"]:checked').forEach(checkbox => {
        currentParams.append('numbers[]', checkbox.value);
    });

        currentParams.delete('page');
        window.location.search = currentParams.toString();
    }
</script>
<script>
    
</script>
   <script src="nav.js"></script>
   <script src="product.js"></script>

   

</body>
</html>
