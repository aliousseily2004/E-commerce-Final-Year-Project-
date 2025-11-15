<?php
$server="localhost";
$username="root";
$paswword="";
$db="final";

try{
    $pdo=new PDO("mysql:host=$server;dbname=$db",$username,$paswword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


}
catch(PDOException $exception){
    echo "Connection to database failed! Error:".$exception->getMessage();
    
    die();

}