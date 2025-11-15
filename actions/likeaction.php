<?php
session_start();
require "../connection.php";

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['id'];
$commentId = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;

if ($commentId <= 0) {
    $_SESSION['error'] = "Invalid comment.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Check current like status
    $checkStmt = $pdo->prepare("SELECT is_liked FROM likes WHERE user_id = ? AND comment_id = ?");
    $checkStmt->execute([$userId, $commentId]);
    $currentStatus = $checkStmt->fetch();

    if ($currentStatus) {
        // Toggle like status
        $newStatus = $currentStatus['is_liked'] ? 0 : 1;
        $updateStmt = $pdo->prepare("UPDATE likes SET is_liked = ? WHERE user_id = ? AND comment_id = ?");
        $updateStmt->execute([$newStatus, $userId, $commentId]);
        
        // Update comment like count
        $countChange = $newStatus ? 1 : -1;
        $updateComment = $pdo->prepare("UPDATE comments SET like_count = like_count + ? WHERE id = ?");
        $updateComment->execute([$countChange, $commentId]);
    } else {
        // Create new like entry
        $insertStmt = $pdo->prepare("INSERT INTO likes (user_id, comment_id, is_liked) VALUES (?, ?, 1)");
        $insertStmt->execute([$userId, $commentId]);
        
        // Update comment like count
        $updateComment = $pdo->prepare("UPDATE comments SET like_count = like_count + 1 WHERE id = ?");
        $updateComment->execute([$commentId]);
    }

    $pdo->commit();
    $_SESSION['success'] = $currentStatus['is_liked'] ?? false ? "Unliked comment!" : "Liked comment!";

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>