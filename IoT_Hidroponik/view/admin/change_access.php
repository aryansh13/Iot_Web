<?php
// Pastikan ada parameter yang diterima dari tautan
if(isset($_GET['user_id']) && isset($_GET['access'])) {
    // Ambil nilai parameter dari tautan
    $user_id = $_GET['user_id'];
    $new_access = $_GET['access'];

    // Lakukan validasi parameter (misalnya pastikan $new_access adalah 0 atau 1)
    if($new_access != '0' && $new_access != '1') {
        // Redirect kembali ke halaman sebelumnya jika parameter tidak valid
        header("Location: userManagement.php");
        exit;
    }

    // Lakukan koneksi ke database (sesuai dengan konfigurasi Anda)
    include '../../config/config.php';
    // Buat objek database
    $database = new Database();

    // Lakukan query SQL untuk memperbarui status akses pengguna
    $update_query = "UPDATE users SET access = '$new_access' WHERE id = '$user_id'";
    $result = $database->koneksi->query($update_query);

    // Periksa apakah query berhasil dijalankan
    if($result) {
        // Redirect kembali ke halaman sebelumnya setelah berhasil mengubah status akses
        header("Location: userManagement.php");
        exit;
    } else {
        // Tampilkan pesan kesalahan jika terjadi masalah dalam menjalankan query
        echo "Error: " . $database->koneksi->error;
    }

    // Tutup koneksi database
    $database->koneksi->close();
} else {
    // Redirect kembali ke halaman sebelumnya jika parameter tidak lengkap
    header("Location: userManagement.php");
    exit;
}
?>
