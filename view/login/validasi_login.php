<?php 
    include "../../config/config.php";
    $db = new database();
    $email = $_POST['email'];
    $password = $_POST['password'];

    foreach($db->login($email,$password) as $x){
        session_start();
        $_SESSION["email"] = $email;
        $_SESSION["password"] = $password;
        $role = $x['role'];
        $pass = $x['password'];
        //memeriksa user login sebagai admin atau user
        if(($role == '0') AND ($password == $pass)){
            header('Location: ../admin/adminDashboard.php');
        }
        else if(($role == '1') AND ($password == $pass)){
            header('Location: ../user/userDashboard.php');
        }
        else {
            header('Location: login.php');
        }
    }
?>