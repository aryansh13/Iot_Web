<?php
// Periksa jika request method adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Gunakan password_hash untuk menyimpan password yang di-hash
    $deviceName = $_POST['deviceName'];
    $deviceRequirements = $_POST['deviceRequirement'] ?? [];

    // Memastikan semua kolom diisi
    if (empty($username) || empty($email) || empty($password) || empty($deviceName) || count($deviceRequirements) < 3) {
        echo "Semua kolom harus diisi";
        exit();
    }

    // Data yang akan dikirim ke API dalam format JSON
    $data = array(
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'deviceName' => $deviceName,
        'device_requirements1' => isset($deviceRequirements[0]) ? $deviceRequirements[0] : '',
        'device_requirements2' => isset($deviceRequirements[1]) ? $deviceRequirements[1] : '',
        'device_requirements3' => isset($deviceRequirements[2]) ? $deviceRequirements[2] : ''
    );

    // Mengonversi array data ke dalam format JSON
    $jsonData = json_encode($data);

    // Setup HTTP header
    $header = array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    );

    // Setup options untuk request
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => implode("\r\n", $header),
            'content' => $jsonData
        )
    );

    // Buat context untuk request
    $context = stream_context_create($options);

    // Kirim request ke endpoint API
    $result = file_get_contents('http://localhost:3000/register', false, $context);

    // Decode response JSON
    $responseData = json_decode($result, true);

    // Cek status response
    if ($responseData['status'] === 'success') {
        // Registrasi berhasil, redirect ke halaman login
        header('Location: ../login/login.php');
        exit();
    } else {
        // Registrasi gagal, tampilkan pesan error
        echo "Registrasi gagal: " . $responseData['message'];
    }
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
    let count = 1;

    addMoreBtn.addEventListener('click', function() {
        const newInputGroup = document.createElement('div');
        newInputGroup.className = 'input-group mt-3';

        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.className = 'form-control form-control-user';
        newInput.placeholder = 'Device Requirement';
        newInput.name = 'deviceRequirement[]'; // Diberi nama seperti ini agar menjadi array di PHP

        const removeBtn = document.createElement('div');
        removeBtn.className = 'input-group-append';
        removeBtn.innerHTML = '<button class="btn btn-danger remove-btn p-15" type="button">Remove</button>';

        newInputGroup.appendChild(newInput);
        newInputGroup.appendChild(removeBtn);
        deviceRequirementsContainer.appendChild(newInputGroup);

        count++;
        if (count > 3) {
            addMoreBtn.style.display = 'none'; // Sembunyikan tombol jika sudah mencapai batas
        }
    });

    deviceRequirementsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-btn')) {
            e.target.parentElement.parentElement.remove();
            count--;
            addMoreBtn.style.display = 'block'; // Tampilkan tombol kembali jika dihapus
        }
    });
</script>


</body>

</html>
