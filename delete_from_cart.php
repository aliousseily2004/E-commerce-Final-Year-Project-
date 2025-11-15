<?php
session_start();
require "connection.php"; // Ensure this file connects to your database

// Check if user is logged in and product_id is set
if (isset($_SESSION['id']) && isset($_POST['product_id'])) {
    $userId = $_SESSION['id'];
    $productId = $_POST['product_id'];

    // Prepare the SQL statement to delete the product from the wishlist
    $deleteStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $deleteStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $deleteStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);

    // Execute the delete statement
    if ($deleteStmt->execute()) {
        // Redirect back to the wishlist page with a success message
        header("Location: /final/product.php");
        exit();
    } }
?>
