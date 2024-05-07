<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Menggunakan password_hash untuk menyimpan password yang di-hash
    $deviceName = $_POST['deviceName'];
    $deviceRequirements = array_filter($_POST['deviceRequirement'] ?? []);

    // Data yang akan dikirim ke API
    $postData = array(
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'deviceName' => $deviceName,
        'device_requirements1' => isset($deviceRequirements[0]) ? $deviceRequirements[0] : '',
        'device_requirements2' => isset($deviceRequirements[1]) ? $deviceRequirements[1] : '',
        'device_requirements3' => isset($deviceRequirements[2]) ? $deviceRequirements[2] : ''
    );

    // Setup cURL
    $ch = curl_init('http://localhost:8080/register');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

    // Eksekusi cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Cek jika request berhasil
    if ($httpCode == 200) {
        // Registrasi berhasil, redirect ke halaman login
        header('Location: ../login/login.php');
        exit();
    } else {
        // Registrasi gagal, tampilkan pesan error
        echo "Registrasi gagal: " . $response;
    }

    // Tutup cURL
    curl_close($ch);
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
        <div class="card border-0 shadow-lg m-5 ml-auto mr-auto" style="width: 40rem;">
            <div class="card-body ">
                <!-- Nested Row within Card Body -->
                <div class="flex-column">
                    <div class="col-lg-12">
                        <div class="p-2">
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
                                <!-- Device Requirement hide -->
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div id="deviceRequirementsContainer" class="input-group">
                                            <input type="text" class="form-control form-control-user" name="deviceRequirement[]" id="deviceRequirement" placeholder="Device Requirement">
                                            <div class="input-group-append" style="border-radius: 1;">
                                                <button type="button" class="btn btn-primary" id="addMoreBtn">Add More</button>
                                            </div>
                                        </div>
                                    </div>
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

    <!-- Add More Device -->
    <script>
    const addMoreBtn = document.getElementById('addMoreBtn');
    const deviceRequirementsContainer = document.getElementById('deviceRequirementsContainer');

    addMoreBtn.addEventListener('click', function() {
    const newInputGroup = document.createElement('div');
    newInputGroup.className = 'input-group mt-3';

    const newInput = document.createElement('input');
    newInput.type = 'text';
    newInput.className = 'form-control form-control-user';
    newInput.placeholder = 'Device Requirement';
    newInput.name = 'deviceRequirement[]';

    const removeBtn = document.createElement('div');
    removeBtn.className = 'input-group-append';
    removeBtn.innerHTML = '<button class="btn btn-danger remove-btn p-15" type="button">Remove</button>';

    newInputGroup.appendChild(newInput);
    newInputGroup.appendChild(removeBtn);
    deviceRequirementsContainer.appendChild(newInputGroup);
});

deviceRequirementsContainer.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-btn')) {
        e.target.parentElement.parentElement.remove();
    }
});
</script>

</body>

</html>
