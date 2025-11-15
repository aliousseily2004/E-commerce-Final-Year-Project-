<?php 
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Start the session if it hasn't been started
        require "../connection.php";
    
        $isLoggedIn = isset($_SESSION['id']); 

    }
  
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
  $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT); // Use session ID
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
   
    if (isset($_GET['id'])) {
        $productId = $_GET['id'];
    
        // Fetch product details from the database
        $stmt = $pdo->prepare("SELECT * FROM product WHERE id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } else {
        echo "No product ID specified.";
        exit;
    }
    if ($isLoggedIn) {
        $userId = $_SESSION['id'];

        // Check if the product is already in the wishlist
        $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) { // If not already in wishlist
            // Insert into wishlist
            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo "Product added to wishlist.";
            } else {
                echo "Failed to add product to wishlist.";
            }
        } else {
            echo "Product is already in your wishlist.";
        }
    } else {
        echo "You must be logged in to add products to your wishlist.";
    }


   