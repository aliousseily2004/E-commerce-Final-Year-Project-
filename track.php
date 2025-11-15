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
    <title>Track Your Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shipping.css">
    <link rel="stylesheet" href="track.css">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="footer.css">

    <style>
        #order-details {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        #order-details h3 {
            margin-top: 0;
            color: #333;
        }

        #order-details p {
            margin-bottom: 8px;
        }

        #order-details strong {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <?php
   require "nav.php";
    $orderDetails = null;

    if (isset($_GET['order_id'])) {
        $orderId = filter_var($_GET['order_id'], FILTER_SANITIZE_NUMBER_INT);

        try {
            $stmt = $pdo->prepare("SELECT order_id, order_date, price, status FROM orders WHERE order_id = :order_id");
            $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log the error for debugging purposes
            error_log("Database Error: " . $e->getMessage());
            $errorMessage = "An error occurred while retrieving order information.";
        }
    }
    ?>

    <div class="container">
        <h1>Track Your Order</h1>
        <p>Please enter your order ID below to track the status of your order.</p>

        <form method="GET" action="">
            <label for="order-id">Order ID:</label>
            <input type="text" id="order-id" name="order_id" placeholder="Enter your order ID" value="<?php echo isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : ''; ?>">
            <button type="submit">Track Order</button>
        </form>

        <div id="order-status" style="margin-top: 20px;">
            <?php if (isset($errorMessage)): ?>
                <p style="color: red;"><?php echo $errorMessage; ?></p>
            <?php elseif ($orderDetails): ?>
                <div id="order-details">
                    <h3>Order Details for Order #<?php echo htmlspecialchars($orderDetails['order_id']); ?></h3>
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($orderDetails['order_date']); ?></p>
                    <p><strong>Price:</strong> $<?php echo htmlspecialchars(number_format($orderDetails['price'], 2)); ?></p>
                    <p><strong>Order Status:</strong> <span style="font-weight: bold; color: <?php
                        switch (strtolower($orderDetails['status'])) {
                            case 'active':
                                echo 'green';
                                break;
                            case 'cancelled':
                                echo 'red';
                                break;
                            default:
                                echo 'black';
                        }
                    ?>;"><?php echo htmlspecialchars($orderDetails['status']); ?></span></p>
                    </div>
            <?php elseif (isset($_GET['order_id'])): ?>
                <p style="color: orange;">No order found with the Order ID: <?php echo htmlspecialchars($_GET['order_id']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php
    require "footer.php";
    ?>

    <script src="nav.js"></script>
    <script src="index.js"></script>
</body>
</html>