<?php
// actions/delete_comment.php

session_start();
require '../connection.php';

if (!isset($_SESSION['id'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: /final/login.php"); // Adjust the path as needed
    exit();
}

if (isset($_POST['comment_id']) && is_numeric($_POST['comment_id'])) {
    $commentIdToDelete = $_POST['comment_id'];
    $loggedInUserId = $_SESSION['id'];

    // Verify if the logged-in user owns the comment
    $checkOwnershipQuery = "SELECT user_id FROM comments WHERE id = ?";
    $checkOwnershipStmt = $pdo->prepare($checkOwnershipQuery);
    $checkOwnershipStmt->execute([$commentIdToDelete]);
    $comment = $checkOwnershipStmt->fetch(PDO::FETCH_ASSOC);

    if ($comment && $comment['user_id'] == $loggedInUserId) {
        try {
            // Start transaction to ensure atomicity (delete replies then comment)
            $pdo->beginTransaction();

            // Delete all replies associated with the comment
            $deleteRepliesQuery = "DELETE FROM replies WHERE comment_id = ?";
            $deleteRepliesStmt = $pdo->prepare($deleteRepliesQuery);
            $deleteRepliesStmt->execute([$commentIdToDelete]);

            // Delete the comment itself
            $deleteCommentQuery = "DELETE FROM comments WHERE id = ?";
            $deleteCommentStmt = $pdo->prepare($deleteCommentQuery);
            $deleteCommentStmt->execute([$commentIdToDelete]);

            // Commit the transaction
            $pdo->commit();

            // Redirect back to the product page or wherever appropriate
            $productId = isset($_GET['product_id']) ? $_GET['product_id'] : null;
            if ($productId) {
                header("Location: /final/product_details.php?product_id=" . $productId . "#comments-section"); // Adjust path and anchor
            } else {
                header("Location: /final/index.php"); // Redirect to homepage if no product ID
            }
            exit();

        } catch (PDOException $e) {
            // Rollback the transaction on error
            $pdo->rollBack();
            // Handle the error appropriately (e.g., log it, display a message)
            echo "Error deleting comment and replies: " . $e->getMessage();
        }
    } else {
        // Handle case where the user doesn't own the comment
        echo "You are not authorized to delete this comment.";
    }
} else {
    // Handle case where comment_id is missing or invalid
    echo "Invalid comment ID.";
}

?>