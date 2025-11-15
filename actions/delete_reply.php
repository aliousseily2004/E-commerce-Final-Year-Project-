<?php
// actions/delete_reply.php

session_start();
require '../connection.php';

if (!isset($_SESSION['id'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: /final/login.php"); // Adjust the path as needed
    exit();
}

if (isset($_POST['reply_id']) && is_numeric($_POST['reply_id'])) {
    $replyIdToDelete = $_POST['reply_id'];
    $loggedInUserId = $_SESSION['id'];

    // Verify if the logged-in user owns the reply
    $checkOwnershipQuery = "SELECT user_id, comment_id FROM replies WHERE id = ?";
    $checkOwnershipStmt = $pdo->prepare($checkOwnershipQuery);
    $checkOwnershipStmt->execute([$replyIdToDelete]);
    $reply = $checkOwnershipStmt->fetch(PDO::FETCH_ASSOC);

    if ($reply && $reply['user_id'] == $loggedInUserId) {
        try {
            // Delete the reply
            $deleteReplyQuery = "DELETE FROM replies WHERE id = ?";
            $deleteReplyStmt = $pdo->prepare($deleteReplyQuery);
            $deleteReplyStmt->execute([$replyIdToDelete]);

            // Redirect back to the product page or wherever appropriate
            $productIdQuery = "SELECT product_id FROM comments WHERE id = ?";
            $productIdStmt = $pdo->prepare($productIdQuery);
            $productIdStmt->execute([$reply['comment_id']]);
            $commentProductIdResult = $productIdStmt->fetch(PDO::FETCH_ASSOC);

            if ($commentProductIdResult && isset($commentProductIdResult['product_id'])) {
                header("Location: /final/product.php");
            } 
            exit();

        } catch (PDOException $e) {
            // Handle the error appropriately (e.g., log it, display a message)
            echo "Error deleting reply: " . $e->getMessage();
        }
    } else {
        // Handle case where the user doesn't own the reply
        echo "You are not authorized to delete this reply.";
    }
} else {
    // Handle case where reply_id is missing or invalid
    echo "Invalid reply ID.";
}

?>