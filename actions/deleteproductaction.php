<?php
session_start();
require "../connection.php";

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate input
        if (isset($_POST["product_id"]) && !empty($_POST["product_id"])) {
            $product_id = $_POST["product_id"];
            
            // Correct SQL preparation and binding
            $sql = 'DELETE FROM product WHERE id = :product_id';
            $stmt = $pdo->prepare($sql);
            
            // Correct parameter binding
            $stmt->bindParam(':product_id', $product_id);
            
            // Execute the statement
            $Delete = $stmt->execute();
            
            if ($Delete) {
                // Redirect to dashboard.php after successful deletion
                header("Location: ../dashbord.php");
                exit();
            } else {
                // Optionally handle the case where deletion failed
                header("Location: ../dashbord.php");
                exit();
            }
            
            // Optionally, you can redirect or provide feedback
           
        }
    }
} catch (PDOException $exception) {
    // Log the error in a production environment
    error_log("Database Error: " . $exception->getMessage());
    
    // Redirect with error status
    header("Location: ../index.php?status=db_error");
    exit();
}
