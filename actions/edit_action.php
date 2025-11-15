<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "../connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $street_name = $_POST['street_name'];
    $city = $_POST['city'];

    $target = "C:/xampp/htdocs/final/userprofile/" . $_FILES['profile_photo']['name'];
    $fileType = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    if($fileType != "jpeg" && $fileType != "png" && $fileType != "jpg" && $fileType != "gif"){
        die("unsupported file type");
    }
  
    // if($_FILES['profile_photo']['size']>10000){
    //     die("File size too large");
    // }
    if(!getimagesize($_FILES['profile_photo']['tmp_name'])){
        die("image invalid");
    }

   
    move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target);
}

try {
    // Update user information in the database
    $sql = "UPDATE users SET name=:name, email=:email, password=:password, phone=:phone, street=:street_name, city=:city, photo=:profile_photo WHERE email=:current_email";
    $stmt = $pdo->prepare($sql);
    
    // Assuming you have the current user's email stored in the session
    $current_email = $_SESSION['email']; // Adjust this according to your session variable
    
    // Hash the password if it's being changed
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':street_name', $street_name);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':profile_photo', $target); // Bind the image path
    $stmt->bindParam(':current_email', $current_email);
    
    // Execute the statement
    $stmt->execute();
    
 header("Location: ../profile.php");
} catch (PDOException $exception) {
    echo "Error executing query: " . $exception->getMessage();
} catch (Exception $exception) {
    echo "Error: " . $exception->getMessage();
}

?>
