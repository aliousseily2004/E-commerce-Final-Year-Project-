<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Login</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="FAQ.css">
    <link rel="stylesheet" href="about.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="footer.css">
   
</head>

<body>
    <?php
      require "nav.php";
    ?>

    <div class="container">
   <div class="Login">
        <h2> <a href="login.php" style="color: red;">Login</a> | <a href="register.php">Register</a>  </h2>
      
        <form id="registerForm" action="./actions/loginaction.php" method="POST">
        
            <div>
                <label for="LoginEmail">Email:</label>
                <input type="email" id="LoginEmail" name="email" required>
            </div>
            <?php
                        if (isset($_GET['error']) && $_GET['error'] === 'invalidemail') {
                            echo '<div class="error-message">Invalid email format</div>';
                        }
                    ?>
            <div>
                <label for="LoginPassword">Password:</label>
                <div class="password-container">
                    <input type="password" id="LoginPassword" name="password" required>
                    <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                </div>
            </div>
            <?php
                    if (isset($_GET['error']) && $_GET['error'] === 'loginfailed') {
                        echo '<div class="error-message">Invalid username or password</div>';
                    }
                ?>

            <div>
                <button type="submit">Login</button>
            </div>
        </form>
    </div></div>
    <?php require "footer.php"; ?>
    <script src="nav.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('LoginPassword');
            const passwordType = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', passwordType);
            this.classList.toggle('fa-eye-slash'); // Toggle the eye slash icon
        });
    
      
    </script>
</body>
</html>
