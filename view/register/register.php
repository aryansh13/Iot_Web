<?php
include "../../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new database();

    // Mendapatkan data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Menggunakan password_hash untuk menyimpan password yang di-hash
    $deviceName = $_POST['deviceName'];
    $deviceRequirement = $_POST['device_requirements'];

    // Menambahkan user baru ke dalam tabel users
    $db->registerUser($username, $email, $password);

    // Mendapatkan ID user yang baru saja ditambahkan
    $userId = $db->getUserIdByEmail($email);

    // Menambahkan device user ke dalam tabel user_devices
    $db->addUserDevice($userId, $deviceName, $deviceRequirement);

    // Redirect ke halaman login setelah registrasi berhasil
    header('Location: ../login/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>IoT Hidroponik</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card border-0 shadow-lg my-5 mb-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row flex-column">

                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" name="username" id="username" placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" name="email" id="email" placeholder="Email Address">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <input type="text" id="deviceName" name="deviceName" class="form-control form-control-user" placeholder="Device Name">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="device_requirements" name="device_requirements" placeholder="Device Requirement">
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Account
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="../login/login.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../../js/sb-admin-2.min.js"></script>

</body>

</html>
