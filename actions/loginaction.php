<?php
session_start();
require "../connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../login.php?error=invalidemail");
    exit();
}


if (empty($email) || empty($password)) {
    echo "Please enter both email and password";
    exit();
}

try {
    $sql = "SELECT * FROM users WHERE email=:email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['LoggedIn'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        
        header("Location: ../product.php");
         exit();
    } else {
        header("Location: ../login.php?error=loginfailed");
        exit();
    }
} catch (PDOException $exception) {
    echo "Error executing query: " . $exception->getMessage();
} catch (Exception $exception) {
    echo "Error: " . $exception->getMessage();
}


?>