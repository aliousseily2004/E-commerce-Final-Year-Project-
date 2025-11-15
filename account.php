<?php

   if (session_status() == PHP_SESSION_NONE) {
    session_start();
    require "connection.php"; // Ensure this file connects to your database
}
$isLoggedIn = isset($_SESSION['id']);
if (!$isLoggedIn) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit(); // Ensure that the script stops execution after the redirect
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="account.css">
    <link rel="stylesheet" href="footer.css">
    
  
</head>
<body>
    <?php
    require "nav.php";

    ?>
    
    <section class="account-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h2 class="mb-4">Your Account</h2>
                    <div class="account-options">
                        <div class="account-card">
                            <a href="login.php">
                                <i class="fas fa-sign-in-alt"></i>
                            </a>
                            <h3>Login</h3>
                            <p>Access your personal account</p>
                            <a href="login.php" class="btn btn-outline-danger">
                                Login Now <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="account-card">
                            <a href="register.php">
                            <i class="fas fa-user-plus"   ></i></a>
                            <h3>Register</h3>
                            <p>Create a new account with us</p>
                            <a href="register.php" class="btn btn-outline-danger">
                                Register Now <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="account-card">
                            <a href="profile.php">
                            <i class="fas fa-user-circle"></i></a>
                            <h3>My Profile</h3>
                            <p>Manage your personal details</p>
                            <a href="profile.php" class="btn btn-outline-danger">
                                View Profile <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

    <?php
   require "footer.php";

   ?>
   <script src="nav.js"></script>
   <script src="index.js"></script>
   
</body>
</html>