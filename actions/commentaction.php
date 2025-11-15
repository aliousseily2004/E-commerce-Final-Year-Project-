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
$userComment = isset($_POST['userComment']) ? trim($_POST['userComment']) : ''; // Ensure we get the comment

// Check if the comment is not empty and limit it to 255 characters
if (empty($userComment) || strlen($userComment) > 255) {
    $_SESSION['error'] = "Comment cannot be empty and must be less than 255 characters.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}




$pdo->beginTransaction();

try {
    // Insert new comment with timestamp
    $insertQuery = "INSERT INTO comments (user_id, product_id, comment, commentDate) VALUES (?, ?, ?, NOW())";
    $insertStmt = $pdo->prepare($insertQuery);
    $insertStmt->execute([$userId, $productId, $userComment]);

    // Commit the transaction
    $pdo->commit();

    $_SESSION['message'] = "Thank you for your comment!";
    
    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

    
} catch (PDOException $e) {
    // Rollback the transaction in case of error
    $pdo->rollBack();
    
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
