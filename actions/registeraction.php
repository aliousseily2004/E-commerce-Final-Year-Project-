<?php
session_start();
require "../connection.php";
function validatePhoneNumber($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (!ctype_digit($phone)) {
        return false;
    }
    if (strlen($phone) != 8) { // Changed to check for exactly 8 digits
        return false;
    }

    return true;
}

// Initialize variables to store input values for re-populating the form
$name = $email = $address = $city = $phone = '';

if($_SERVER['REQUEST_METHOD']=="POST"){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];


    if (empty(trim($name)) ||
        empty(trim($email)) ||
        empty(trim($address)) ||
        empty(trim($city)) ||
        empty(trim($phone)) ||
        empty(trim($password))) {

        header("Location: ../register.php?err=1&name=".urlencode($name)."&email=".urlencode($email)."&address=".urlencode($address)."&city=".urlencode($city)."&phone=".urlencode($phone));
        exit();
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../register.php?err=2&name=".urlencode($name)."&email=".urlencode($email)."&address=".urlencode($address)."&city=".urlencode($city)."&phone=".urlencode($phone));
        exit();
    }

    if(strlen($password) < 8){
        header("Location: ../register.php?err=3&name=".urlencode($name)."&email=".urlencode($email)."&address=".urlencode($address)."&city=".urlencode($city)."&phone=".urlencode($phone));
        exit();
    }
    if (!validatePhoneNumber($phone)) {
        header("Location: ../register.php?err=5&name=".urlencode($name)."&email=".urlencode($email)."&address=".urlencode($address)."&city=".urlencode($city)."&phone=".urlencode($phone));
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO users (name, email, street, city , phone, password)
                VALUES (:name, :email, :address, :city , :phone, :password)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();
        $_SESSION['LoggedIn'] = true;
        $_SESSION['id'] = $pdo->lastInsertId();
        $_SESSION['email'] = $email;


        header("Location: ../product.php");
        exit();
    }
    catch (PDOException $e){
        if($e->getCode() == 23000){
            header("Location: ../register.php?err=4&name=".urlencode($name)."&email=".urlencode($email)."&address=".urlencode($address)."&city=".urlencode($city)."&phone=".urlencode($phone));
            exit();
        }
        header("Location: ../register.php?err=0&name=".urlencode($name)."&email=".urlencode($email)."&address=".urlencode($address)."&city=".urlencode($city)."&phone=".urlencode($phone));
        exit();
    }
}
?>