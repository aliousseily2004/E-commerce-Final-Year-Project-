<?php
session_start();
require "connection.php";



$orderId = intval($_POST['order_id']);
$userId = $_SESSION['id'];

try {
    $pdo->beginTransaction();

    // Verify order is cancellable
    $stmt = $pdo->prepare("
    SELECT o.*
    FROM orders o
    WHERE o.order_id = :order_id
    AND o.user_id = :user_id
    AND o.status = 'active'
    AND DATE(o.order_date) = CURDATE()
");
    $stmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
    $order = $stmt->fetch();

    if (!$order) {
        throw new Exception("Order cannot be cancelled");
    }

    // Update order status
    $updateOrder = $pdo->prepare("
        UPDATE orders 
        SET status = 'cancelled' 
        WHERE order_id = :order_id
    ");
    $updateOrder->execute([':order_id' => $orderId]);

    // Restore stock
    $restoreStock = $pdo->prepare("
        UPDATE product 
        SET stock = stock + :quantity 
        WHERE id = :product_id
    ");
    $restoreStock->execute([
        ':quantity' => $order['quantity'],
        ':product_id' => $order['product_id']
    ]);

    $pdo->commit();
    header("Location: profile.php");
    exit;

}  catch (Exception $e) {
    $pdo->rollBack();
    
    // Redirect to profile with an error message (optional)
    header("Location: profile.php?error=Order+cannot+be+cancelled");
    exit;
}
?>