<?php 
    include "../../config/config.php";
    $db = new database();
    session_start(); // Start session before any output

    if(isset($_POST['email']) && isset($_POST['password'])){
        $email = $_POST['email'];
        $password = $_POST['password'];

        $result = $db->login($email, $password);

        if($result){
            foreach($result as $x){
                $_SESSION["email"] = $email;
                $_SESSION["password"] = $password;
                $role = $x['role'];
                $pass = $x['password'];
                $access = $x['access'];
                //memeriksa user login sebagai admin atau user
                if(($role == '0') && ($password == $pass)){
                    header('Location: ../admin/adminDashboard.php');
                    exit; // Stop further execution
                }
                else if(($role == '1') && ($password == $pass)){
                    if($access == true){
                        header('Location: ../user/userDashboard.php');
                        exit; // Stop further execution
                    } else {
                        header('Location: ../404/404.php');
                        exit; // Stop further execution
                    }
                }
            }
        }
    }
// Redirect to login page if login fails
header('Location: login.php');
exit; // Stop further execution
?>
