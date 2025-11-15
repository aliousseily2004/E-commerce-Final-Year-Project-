<?php
session_start();
require "../connection.php"; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['comment_id']) && isset($_POST['comment_text'])) {
        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
        $commentText = filter_input(INPUT_POST, 'comment_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $userId = $_SESSION['id'];

        if ($commentId && $commentText) {
            $stmt = $pdo->prepare("UPDATE comments SET comment = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$commentText, $commentId, $userId]);

            if ($stmt->rowCount() > 0) {
                // Comment updated successfully
                header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the product page
                exit();
            } else {
                // Error updating comment or comment does not belong to the user
                header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=edit_comment_failed");
                exit();
            }
        } else {
            // Invalid comment ID or text
            header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=invalid_comment_data");
            exit();
        }
    } elseif (isset($_POST['reply_id']) && isset($_POST['reply_text'])) {
        $replyId = filter_input(INPUT_POST, 'reply_id', FILTER_SANITIZE_NUMBER_INT);
        $replyText = filter_input(INPUT_POST, 'reply_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $userId = $_SESSION['id'];

        if ($replyId && $replyText) {
            $stmt = $pdo->prepare("UPDATE replies SET reply_text = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$replyText, $replyId, $userId]);

            if ($stmt->rowCount() > 0) {
                // Reply updated successfully
                header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the product page
                exit();
            } else {
                // Error updating reply or reply does not belong to the user
                header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=edit_reply_failed");
                exit();
            }
        } else {
            // Invalid reply ID or text
            header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=invalid_reply_data");
            exit();
        }
    } else {
        // No comment or reply data received
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=no_edit_data");
        exit();
    }
} else {
    // If the request method is not POST
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>