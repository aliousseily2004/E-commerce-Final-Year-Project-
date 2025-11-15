<?php
session_start();
require "../connection.php"; // This should return a PDO instance

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}



$userId = $_SESSION['id'];
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$userRating = isset($_POST['userRating']) ? intval($_POST['userRating']) : 0;

// Validate input
if ($productId <= 0 || $userRating < 1 || $userRating > 5) {
    $_SESSION['error'] = "Invalid rating data";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

try {
    // Begin transaction
    $checkQuery = "SELECT * FROM rating WHERE user_id = ? AND product_id = ?";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([$userId, $productId]);
    
    if ($checkStmt->rowCount() > 0) {
        // Update existing rating
        $updateQuery = "UPDATE rating SET rating = ? WHERE user_id = ? AND product_id = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([$userRating, $userId, $productId]);
        $_SESSION['message'] = "Your rating has been updated!";
    } else {
        // Insert new rating
        $insertQuery = "INSERT INTO rating (user_id, product_id, rating) VALUES (?, ?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([$userId, $productId, $userRating]);
        $_SESSION['message'] = "Thank you for rating this product!";
    }
    
    // Calculate new average rating and count for this product
    $calcQuery = "SELECT 
                    AVG(rating) as average_rating, 
                    COUNT(*) as rating_count 
                  FROM rating 
                  WHERE product_id = ?";
    $calcStmt = $pdo->prepare($calcQuery);
    $calcStmt->execute([$productId]);
    $result = $calcStmt->fetch(PDO::FETCH_ASSOC);
    
    // Update product table with new average and count
    $updateProductQuery = "UPDATE rating 
                          SET average_rating = ?, 
                              rating_count = ? 
                          WHERE product_id = ?";
    $updateProductStmt = $pdo->prepare($updateProductQuery);
    $updateProductStmt->execute([
        round($result['average_rating'], 2), // Round to 1 decimal place
        $result['rating_count'],
        $productId
    ]);
    
   
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
    
} catch (PDOException $e) {
    
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}