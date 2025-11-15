<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="product.css">
    <?php
session_start();
require "connection.php";
    ?>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--dark-color);
            color: white;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .sidebar-logo img {
            width: 50px;
            margin-right: 15px;
            border-radius: 50%;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 15px;
        }

        .sidebar-nav a {
            color: var(--light-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar-nav a:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .sidebar-nav a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Area */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-icon {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: bold;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .data-table th, 
        .data-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .data-table th {
            background-color: var(--primary-color);
            color: white;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        /* Button Styles */
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-edit {
            background-color: var(--warning-color);
            color: white;
        }

        .btn-edit:hover {
            background-color: #d35400;
        }

        /* Rating Styles */
        .rating {
            display: flex;
            align-items: center;
        }

        .rating-stars {
            color: #f1c40f;
            margin-right: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
   
</head>
<?php
// Assuming PDO connection is established
try {
    // Fetch Products
    $productStmt = $pdo->prepare("SELECT COUNT(*) as product_count FROM product");
    $productStmt->execute();
    $productCount = $productStmt->fetch(PDO::FETCH_ASSOC)['product_count'];

    // Fetch Orders
    $orderStmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders WHERE status='active'");
    $orderStmt->execute();
    $orderCount = $orderStmt->fetch(PDO::FETCH_ASSOC)['order_count'];
    
    $orders = $pdo->prepare("
    SELECT o.order_id, 
           o.user_id AS customer_id,
           u.name AS customer_name,
           o.price,
           o.status 
    FROM orders o
    INNER JOIN users u ON o.user_id = u.id
");
$orders->execute();
$allOrders = $orders->fetchAll(PDO::FETCH_ASSOC);
   

    // Fetch Users
    $userStmt = $pdo->prepare("SELECT COUNT(*) as user_count FROM users");
    $userStmt->execute();
    $userCount = $userStmt->fetch(PDO::FETCH_ASSOC)['user_count'];
    $revenueStmt = $pdo->prepare("SELECT SUM(price) as total_revenue FROM orders WHERE status = 'active'");
    $revenueStmt->execute();
    $totalRevenue = $revenueStmt->fetch(PDO::FETCH_ASSOC)['total_revenue'];

    // If total revenue is NULL, set it to 0
    $totalRevenue = $totalRevenue ? $totalRevenue : 0;
    

    $query = "
        SELECT DISTINCT p.*, pa.*, r.average_rating 
        FROM product p
        LEFT JOIN productsattributes pa ON p.id = pa.product_id
        LEFT JOIN rating r ON p.id = r.product_id
        WHERE 1=1
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
    
} catch (PDOException $e) {
    // Error handling
    error_log("Database Error: " . $e->getMessage());
    $productCount = $orderCount = $userCount = 0;
}
?>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            
            <h2>E-Commerce Admin</h2>
        </div>
        
        <ul class="sidebar-nav">
        <li><a href="#dashboardSection"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="#productsSection"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="#ordersSection"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="#usersSection"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="#commentsSection"><i class="fas fa-comments"></i> Comments</a></li>
        <li><a href="#reviewsSection"><i class="fas fa-star"></i> Reviews</a></li>
        <li><a href="#stockSection"><i class="fas fa-warehouse"></i> Stock</a></li> <!-- Added icon here -->
        

    </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <form action="insertproduct.php" method="POST">
    <div>
        <button class="btn btn-primary" type="submit">Add Product</button>
    </div>
</form>
        </div>

        <section id="dashboardSection" class="dashboard-cards">
        <div style="display: flex; gap: 20px;">
    <div class="dashboard-card" style="flex: 1;">
        <div class="card-header">
            <i class="fas fa-box card-icon"></i>
            <span>Total Products</span>
        </div>
        <div class="card-value" id="totalProductsCount"><?php echo htmlspecialchars($productCount); ?></div>
    </div>
    <div class="dashboard-card" style="flex: 1;">
        <div class="card-header">
            <i class="fas fa-shopping-cart card-icon"></i>
            <span>Total Orders</span>
        </div>
        <div class="card-value" id="totalOrdersCount"><?php echo htmlspecialchars($orderCount); ?></div>
    </div>
</div>
<div style="display: flex; gap: 20px; margin-top: 20px;">
    <div class="dashboard-card" style="flex: 1;">
        <div class="card-header">
            <i class="fas fa-users card-icon"></i>
            <span>Total Users</span>
        </div>
        <div class="card-value" id="totalUsersCount"><?php echo htmlspecialchars($userCount); ?></div>
    </div>
    <div class="dashboard-card" style="flex: 1;">
        <div class="card-header">
            <i class="fas fa-dollar-sign card-icon"></i>
            <span>Total Revenue</span>
        </div>
        <div class="card-value" id="totalRevenueCount"><?php echo htmlspecialchars('$' . number_format($totalRevenue, 2)); ?></div>
    </div>
</div>
<h2>Top Products</h2>
<table class="data-table" id="TopProductsTable">
    <thead>
        <tr>
            <th>Product</th>
            <th>Sales Count</th>
            <th>Total Revenue</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Assuming you have a PDO connection established
        try {
            $sql = "SELECT 
                        p.photo1,
                       
                        p.price,
                        SUM(o.quantity) AS total_quantity,
                        SUM(o.quantity * p.price) AS total_revenue
                    FROM 
                        product p
                    JOIN 
                        orders o ON p.id = o.product_id
                    WHERE 
                        o.status = 'active'
                    GROUP BY 
                        p.id,  p.price, p.photo1
                    ORDER BY 
                        total_revenue DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            // Fetch all results
            $TopProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($TopProducts) {
                foreach ($TopProducts as $row) {
                    echo '<tr>';
                    echo "<td>" . findAndDisplayImage($row["photo1"]) . "</td>"; // Pass product name
                    echo "<td>" . htmlspecialchars($row["total_quantity"]) . "</td>";
                    echo "<td>$" . number_format($row["total_revenue"], 2) . "</td>";
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">No products found.</td></tr>';
            }
        } catch (PDOException $e) {
            echo '<tr><td colspan="3">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
        }
        ?>
    </tbody>
</table>
       
        </section>
     
  


        <section id="productsSection">
        <h2>Products Management</h2>
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
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
    </div>
    <form action="editproduct.php" method="GET" class="icon editproduct-icon">
                <input type="hidden" name="id" value="<?php echo $product['product_id']; ?>">
                <button type="submit" title="Edit Product" style="border: none; background: none; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
            </form>

    <form action="actions/deleteproductaction.php?id=<?php echo $product['product_id']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" class="icon delete-icon">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
        <input type="hidden" name="delete_product" value="1">
        <button type="submit" title="Delete Product" style="border: none; background: none; cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </button>
    </form>
</div>
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

 


$productId = $product['id']; 
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
       
    </div>
    
   

    
    <div class="rating-stars">
        <?php
        // Display empty stars
        for ($i = 1; $i <= 5; $i++) {
            echo '<span class="star">☆</span>';
        }
        ?>
    </div>
 
    <?php
}
?>
    
   

    
    
       
</div>
</div>

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
        </section>


        <section id="ordersSection">
    <h2>Orders Management</h2>
    <table class="data-table" id="ordersTable">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="ordersTableBody">
            <?php if (!empty($allOrders)): ?>
                <?php foreach ($allOrders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['customer_id']) ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['price']) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No orders found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<style>
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .data-table th, .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
      
        .delete-btn {
            background-color: #e53e3e;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #c53030;
        }
        img.user-photo {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
        }
    </style>
<section id="usersSection">
        <h2>Users Management</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Street</th>
                    <th>City</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM users");
                    while($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Process image path
                        $photoPath = 'Unknown_person.jpg'; // Default image
                        
                        if(!empty($user['photo'])) {
                            // Convert server path to web path
                            $absolutePath = $user['photo'];
                            
                            // Check if file exists at original path
                            if(file_exists($absolutePath)) {
                                $photoPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $absolutePath);
                            }
                            
                            // Final check for web-accessible path
                            if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $photoPath)) {
                                $photoPath = 'Unknown_person.jpg';
                            }
                        }

                        // Sanitize output
                        $photoPath = htmlspecialchars($photoPath);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['street']) ?></td>
                            <td><?= htmlspecialchars($user['city']) ?></td>
                            <td>
                                <img src="<?= $photoPath ?>" 
                                     class="user-photo" 
                                     alt="User Photo"
                                     onerror="this.src='Unknown_person.jpg'">
                            </td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="delete_user" class="delete-btn">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                } catch(PDOException $e) {
                    echo "<tr><td colspan='8'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
<?php
// Handle deletion if the form is submitted
if (isset($_POST['delete_user'])) {
   // Ensure that the user ID is set and is a valid integer
   if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
       $user_id_to_delete = (int)$_POST['user_id']; // Cast to integer for safety

       try {
           // Start a transaction for safer deletion
           $pdo->beginTransaction();

           // First, delete related records in other tables (if applicable)
           // For example, delete user's comments
           $delete_comments_stmt = $pdo->prepare("DELETE FROM comments WHERE user_id = :user_id");
           $delete_comments_stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);
           $delete_comments_stmt->execute();

           // Prepare the delete statement for the user
           $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
           $delete_stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);

           // Execute the delete statement
           $delete_result = $delete_stmt->execute();

           // Commit the transaction
           $pdo->commit();

           // Check if any rows were actually deleted
           if ($delete_stmt->rowCount() > 0) {
               // Redirect to prevent form resubmission
               echo "<script>
                       alert('User deleted successfully.');
                       window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                    </script>";
               exit();
           } else {
               echo "<script>alert('No user found with the specified ID.');</script>";
           }

       } catch (PDOException $e) {
           // Rollback the transaction in case of error
           $pdo->rollBack();

           // Log the actual error message for debugging
           error_log("Error deleting user: " . $e->getMessage());

           // Display a user-friendly error message
           echo "<script>
                   alert('Error deleting user. Please try again later.');
                   console.error('" . addslashes($e->getMessage()) . "');
                 </script>";
       }
   } else {
       echo "<script>alert('Invalid user ID.');</script>";
   }
}
?>

<section id="commentsSection">

        <h2>Comments Management</h2>
        <table>

        <thead>

        <tr>
        <th>Product image</th>
        <th>Comment</th>

        <th>User ID</th>

        <th>User Name</th>

        <th>Comment Date</th>

        <th>Like Count</th>
        <th>View Replies</th>

        <th>Delete Comment</th>

        </tr>

        </thead>

        <tbody>

        <?php
// Define the function OUTSIDE of the loop
function findAndDisplayImage($photo1) {
    $possibleLocations = [
        'uploads/' . $photo1,
        'Editedimages/' . $photo1
    ];

    foreach ($possibleLocations as $path) {
        if (file_exists($path)) {
            return '<img src="' . htmlspecialchars($path) . '" alt="Product Image" style="max-width:100px; max-height:100px;">';
        }
    }
    return 'Image not found';
}

try {
    $stmt = $pdo->query("SELECT
    c.comment,
    c.user_id,
    u.name AS user_name,
    c.commentDate,
    c.like_count,
    c.id,
    p.photo1
FROM
    comments c
JOIN
    users u ON c.user_id = u.id
JOIN
    product p ON c.product_id = p.id
ORDER BY
    c.commentDate DESC;");

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . findAndDisplayImage($row["photo1"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["comment"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["user_name"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["commentDate"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["like_count"]) . "</td>";
        echo "<td><a href='view_replies.php?comment_id=" . htmlspecialchars($row["id"]) . "'>View Replies</a></td>"; // View Replies link
        echo "<td>";
echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to delete this comment?\");'>";
echo "<input type='hidden' name='delete_id' value='" . htmlspecialchars($row['id']) . "'>";
echo "<input type='submit' value='Delete' style='cursor:pointer; background-color:#ff4444; color:white; border:none; padding:5px 10px; border-radius:3px;'>";
echo "</form>";
echo "</td>";
      
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7'>Error fetching comments: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
        </tbody>

        </table>



 
</section>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $deleteId = $_POST['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$deleteId]);
        
        // Redirect to refresh the page and prevent form resubmission
        
    } catch (PDOException $e) {
        die("<div class='error'>Error deleting comment: " . htmlspecialchars($e->getMessage()) . "</div>");
    }
}
?>

<style>

 #commentsSection table {
width: 100%;

border-collapse: collapse;

margin-top: 20px;

}



 #commentsSection th, #commentsSection td {

border: 1px solid #ddd;

 padding: 8px;
text-align: left;

}



#commentsSection th {

background-color: #f2f2f2;

}



#commentsSection button {

 padding: 5px 10px;

background-color: #f44336;

color: white;

border: none;

 cursor: pointer;
border-radius: 3px;

 }



 #commentsSection button:hover {

background-color: #d32f2f;

}

</style>
<?php
// Assuming you have a PDO connection established
try {
    $sql = 'SELECT 
        r.rating,
        r.average_rating,
        u.name,
        p.photo1
    FROM 
        rating r
    JOIN 
        users u ON r.user_id = u.id  
    JOIN 
        product p ON r.product_id = p.id';

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all results
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

// Function to generate star SVGs based on rating
function displayStars($rating) {
    $output = '';
    $fullStars = min(5, max(0, floor($rating))); // Ensure rating is between 0 and 5

    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $output .= '<i class="fas fa-star" style="color: gold;"></i>'; // Filled star
    }

    // Empty stars
    $emptyStars = 5 - $fullStars; // Remaining empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $output .= '<i class="far fa-star" style="color: lightgray;"></i>'; // Empty star
    }

    return $output;
}
?>
<section id="reviewsSection">
    <h2>Product Reviews</h2>
    <table class="data-table" id="reviewsTable">
        <thead>
            <tr>
                <th>Product</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Average Rating</th>
            </tr>
        </thead>
        <tbody id="reviewsTableBody">
            <?php
            if ($reviews) {
                foreach ($reviews as $row) {
                    echo '<tr>';
                    echo "<td>" . findAndDisplayImage($row["photo1"]) . "</td>";
                    echo '<td>' . htmlspecialchars($row['name']) . '</td>'; // Display customer name
                    echo '<td>' . displayStars((int)$row['rating']) . '</td>'; // Display stars based on rating
                    echo '<td>' . ($row['average_rating']) . '</td>'; // Display stars based on rating
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">No reviews found.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</section>


<section id="stockSection">
    <h2>Product Stock</h2>
    <table class="data-table" id="StockTable">
        <thead>
            <tr>
                <th>Product</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody id="StockTableBody">
            <?php
            // Assuming you have a PDO connection established
            try {
                $sql = 'SELECT 
                    p.photo1,
                    p.stock 
                FROM 
                    product p
                ORDER BY 
                    p.stock ASC';

                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                // Fetch all results
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($products) {
                    foreach ($products as $row) {
                        echo '<tr>';
                        echo "<td>" . findAndDisplayImage($row["photo1"]) . "</td>";
                        
                        // Check stock and display message if stock is less than 10
                        if ($row['stock'] < 10) {
                            echo '<td>' . htmlspecialchars($row['stock']) . ' <span style="color: red;">You should add products to stock!</span></td>';
                        } else {
                            echo '<td>' . htmlspecialchars($row['stock']) . '</td>'; // Display stock quantity
                        }
                        
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="2">No products found.</td></tr>';
                }
            } catch (PDOException $e) {
                echo '<tr><td colspan="2">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</section>
        
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
        const mainContentSections = document.querySelectorAll('.main-content > section');

        // Function to show a specific section and hide others
        function showSection(sectionId) {
            mainContentSections.forEach(section => {
                section.style.display = 'none';
            });
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }
        }

        // Initially show the dashboard section
        showSection('dashboardSection');

        // Add event listeners to sidebar links
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default link behavior (page reload)
                const targetId = this.getAttribute('href').substring(1); // Get the section ID from the href
                showSection(targetId);

                // Optionally, update the browser's history for navigation
                history.pushState(null, null, '#' + targetId);
            });
        });

        // Handle back/forward button navigation
        window.addEventListener('popstate', function() {
            const hash = window.location.hash.substring(1);
            showSection(hash || 'dashboardSection'); // Show dashboard if no hash
        });
    });
</script>
<script src="index.js"></script>
<script src="product.js"></script>
</body>
</html>