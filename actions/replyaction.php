<?php
session_start();
require "../connection.php";

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_id']) && isset($_POST['reply_text'])) {
    $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
    $replyText = filter_input(INPUT_POST, 'reply_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userId = $_SESSION['id'] ?? null; // Assuming user ID is in the session

    if ($userId && $commentId && !empty($replyText)) {
        $insertQuery = "INSERT INTO replies (comment_id, user_id, reply_text) VALUES (?, ?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([$commentId, $userId, $replyText]);

        // Redirect back to the product page or wherever you were
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '../index.php';
        header("Location: " . $redirectUrl);
        exit();
    } else {
        // Handle errors (e.g., user not logged in, missing data)
        echo "Error: Could not submit reply.";
        // Optionally redirect with an error message
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_reply_id'])) {
    // --- DELETE REPLY LOGIC ---
    $deleteReplyId = filter_input(INPUT_POST, 'delete_reply_id', FILTER_SANITIZE_NUMBER_INT);
    $userId = $_SESSION['id'] ?? null; // Assuming user ID is in the session

    // You might want to add a check to ensure the logged-in user is the owner of the reply
    $checkOwnershipQuery = "SELECT user_id FROM replies WHERE id = ?";
    $checkOwnershipStmt = $pdo->prepare($checkOwnershipQuery);
    $checkOwnershipStmt->execute([$deleteReplyId]);
    $reply = $checkOwnershipStmt->fetch(PDO::FETCH_ASSOC);

    if ($userId && $deleteReplyId && $reply && $reply['user_id'] == $userId) {
        $deleteQuery = "DELETE FROM replies WHERE id = ?";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->execute([$deleteReplyId]);

        // Redirect back
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '../index.php';
        header("Location: " . $redirectUrl);
        exit();
    } else {
        echo "Error: Could not delete reply or unauthorized.";
        // Optionally redirect with an error message
    }
} else {
    // If the request is not a POST with the expected data
    echo "Invalid request.";
}
?>