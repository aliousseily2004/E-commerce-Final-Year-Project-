<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Register</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="nav.css">
  
    <link rel="stylesheet" href="about.css">
    <link rel="stylesheet" href="register.css">
</head>
<body>
<?php
require "nav.php";
?>
     <div class="container">
        <div class="register">
            <h2 style="margin-bottom: 15px;"> 
                <a href="register.php" style="color: red;">Register</a> | <a href="login.php">Login</a>  
            </h2>
            
            <form id="registerForm" action="./actions/registeraction.php" method="POST">
                <div style="margin-bottom: 15px;">
                    <label for="registerName">Name:</label>
                    <input type="text" id="registerName" name="name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="registerEmail">Email:</label>
                    <input type="email" id="registerEmail" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="registerAddress">Street Address:</label>
                    <input type="text" id="registerAddress" name="address" value="<?php echo isset($_GET['address']) ? htmlspecialchars($_GET['address']) : ''; ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="registerCity">City:</label>
                    <input type="text" id="registerCity" name="city" value="<?php echo isset($_GET['city']) ? htmlspecialchars($_GET['city']) : ''; ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="Phone">Phone:</label>
                    <input
                        type="text"
                        id="Phone"
                        name="phone"
                        placeholder="+961 Enter your phone number"
                        maxlength="8"
                        value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>"
                        required
                    >
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="registerPassword">Password:</label>
                    <div class="password-container">
                        <input type="password" id="registerPassword" name="password" required>
                        <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                    </div>
                </div>
                <div style="margin-bottom: 15px;">
                    <!-- Empty div removed as it seemed unnecessary -->
                </div>
                <div style="margin-bottom: 15px;">
                    <button type="submit">Register</button>
                </div>
            </form>
        </div>
    </div>
    <?php
    if (isset($_GET['err'])) {
        $error_message = '';
        switch ($_GET['err']) {
            case 1:
                $error_message = "Please fill in all required fields.";
                break;
            case 2:
                $error_message = "Invalid email address.";
                break;
            case 3:
                $error_message = "Your password must be at least 8 characters long.";
                break;
            case 4:
                    $error_message = "Account already exsit.";
                    break;    
            case 5:
                $error_message = "Invalid phone number.";
                break;    
          
            default:
                $error_message = "An unknown error occurred.";
                break;
        }
        echo "<div class='error' style='text-align: center; color: red; margin-top: 10px; width: 100%; padding: 10px;  border-radius: 5px;'>$error_message</div>";
    }

    ?>

<?php require "footer.php"; ?>
    <script src="nav.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('registerPassword');
            const passwordType = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', passwordType);
            this.classList.toggle('fa-eye-slash'); // Toggle the eye slash icon
        });
    
      
    </script>
</body>
</html>