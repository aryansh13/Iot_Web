<?php

// Fungsi untuk melakukan permintaan HTTP POST ke API
function sendPostRequest($url, $data)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}

// Jika email dan password dikirimkan melalui form
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Data yang akan dikirimkan ke API
    $data = [
        'email' => $email,
        'password' => $password,
    ];

    // Kirim permintaan POST ke API
    $response = sendPostRequest('http://localhost:8080/login', $data);

    // Menangani respon dari API
    $result = json_decode($response, true);

    // Jika login berhasil
    if ($result['status'] === 'success') {
        $user = $result['user'];

        // Simpan user_id ke dalam sesi
        session_start();
        $_SESSION['user_id'] = $user['id'];

        // Redirect ke halaman dashboard
        if ($user['role'] === 0) { // Role admin
            // Lakukan sesuatu untuk pengguna admin
            header('Location: ../admin/adminDashboard.php');
            exit;
        } elseif ($user['role'] === 1) { // Role user
            if ($user['access'] === 1) { // Akses diizinkan
                // Lakukan sesuatu untuk pengguna dengan akses diizinkan
                header('Location: ../user/userDashboard.php');
                exit;
            } else { // Akses tidak diizinkan
                // Lakukan sesuatu untuk pengguna dengan akses tidak diizinkan
                header('Location: ../404/404.php');
                exit;
            }
        }
    } else {
        // Jika login gagal, tampilkan pesan error dari API
        echo $result['message'];
    }
}
