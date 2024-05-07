<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    //Mengambil seluruh data users
    $app->get("/users/", function (Request $request, Response $response) {
        $sql = "SELECT u.id, u.username, u.email, u.role, u.access, ud.deviceName, ud.device_requirements1, ud.device_requirements2, ud.device_requirements3
                FROM users u
                LEFT JOIN user_devices ud ON u.id = ud.user_id
                WHERE u.role = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    //Mengambil data users berdasarkan id
    $app->get("/users/{id}", function (Request $request, Response $response, array $args) {
        $userId = $args['id'];

        $sql = "SELECT u.id, u.username, u.email, u.role, u.access, ud.deviceName, ud.device_requirements1, ud.device_requirements2, ud.device_requirements3
                FROM users u
                LEFT JOIN user_devices ud ON u.id = ud.user_id
                WHERE u.id = :userId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $response->withJson(["status" => "success", "data" => $result], 200);
        } else {
            return $response->withJson(["status" => "error", "message" => "Data tidak ditemukan"], 404);
        }
    });

    // Menambahkan data user
    $app->post("/users/", function (Request $request, Response $response) {
        $data = $request->getParsedBody();

        // Validasi data
        $username = ($data['username'] ?? '');
        $email = ($data['email'] ?? '');
        $password = ($data['password'] ?? '');
        $deviceName = ($data['deviceName'] ?? '');
        $deviceRequirements1 = ($data['device_requirements1'] ?? '');
        $deviceRequirements2 = ($data['device_requirements2'] ?? '');
        $deviceRequirements3 = ($data['device_requirements3'] ?? '');

        // Cek jika data yang dikirimkan kosong
        if (empty($username) || empty($email) || empty($password) || empty($deviceName) || empty($deviceRequirements1) || empty($deviceRequirements2) || empty($deviceRequirements3)) {
            return $response->withJson(['status' => 'error', 'message' => 'Data belum terisi'], 400);
        }

        // Transaksi database
        $this->db->beginTransaction();

        try {
            // Menambahkan data baru ke tabel users
            $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 1)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $userId = $this->db->lastInsertId();

            // Menambahkan data baru ke tabel user_devices
            $sqlDevices = "INSERT INTO user_devices (user_id, deviceName, device_requirements1, device_requirements2, device_requirements3) VALUES (:userId, :deviceName, :deviceRequirements1, :deviceRequirements2, :deviceRequirements3)";
            $stmtDevices = $this->db->prepare($sqlDevices);
            $stmtDevices->bindParam(':userId', $userId);
            $stmtDevices->bindParam(':deviceName', $deviceName);
            $stmtDevices->bindParam(':deviceRequirements1', $deviceRequirements1);
            $stmtDevices->bindParam(':deviceRequirements2', $deviceRequirements2);
            $stmtDevices->bindParam(':deviceRequirements3', $deviceRequirements3);
            $stmtDevices->execute();

            $this->db->commit();

            return $response->withJson(['status' => 'success', 'message' => 'Data berhasil di tambahkan'], 201);
        } catch (PDOException $e) {
            $this->db->rollBack();
            return $response->withJson(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    });

    // Menghapus data user berdasarkan id
    $app->delete("/users/{id}", function (Request $request, Response $response, array $args) {
        $userId = $args['id'];

        // Query untuk menghapus data user dari tabel users
        $sql = "DELETE FROM users WHERE id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $rowCount = $stmt->rowCount();

            if ($rowCount > 0) {
                // Jika data user berhasil dihapus, maka akan menghapus data terkait dari tabel user_devices
                $sqlDevices = "DELETE FROM user_devices WHERE user_id = :userId";
                $stmtDevices = $this->db->prepare($sqlDevices);
                $stmtDevices->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmtDevices->execute();

                return $response->withJson(["status" => "success", "message" => "Data berhasil di hapus"], 200);
            } else {
                return $response->withJson(["status" => "error", "message" => "Data tidak ditemukan"], 404);
            }
        } catch (PDOException $e) {
            return $response->withJson(["status" => "error", "message" => $e->getMessage()], 500);
        }
    });

    // Route untuk melakukan proses login.
    $app->post("/login", function (Request $request, Response $response) {
        $data = $request->getParsedBody();

        // Mengambil data dari request POST dan melakukan validasi.
        $email = ($data['email'] ?? '');
        $password = ($data['password'] ?? '');

        // Jika email atau password kosong, akan mengembalikan pesan error.
        if (empty($email) || empty($password)) {
            return $response->withJson(['status' => 'error', 'message' => 'Username dan password harus diisi'], 400);
        }

        // Cek kecocokan data pengguna dalam database
        $sql = "SELECT u.*, ud.device_requirements1, ud.device_requirements2, ud.device_requirements3 
                FROM users u
                LEFT JOIN user_devices ud ON u.id = ud.user_id
                WHERE u.email = :email AND u.password = :password";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika data ditemukan dalam database, akan mengembalikan pesan sukses berserta data pengguna.
        if ($user) {
            return $response->withJson(['status' => 'success', 'message' => 'Login berhasil', 'user' => $user]);
            // Jika tidak ditemukan, akan mengembalikan pesan error.
        } else {
            return $response->withJson(['status' => 'error', 'message' => 'Username atau password salah'], 401);
        }
    });

    // Mengambil data user_device berdasarkan user_id
    $app->get("/user_device/{user_id}", function (Request $request, Response $response, $args) {
        $user_id = $args['user_id'];

        // Query untuk mengambil data user beserta perangkatnya
        $sql = "SELECT ud.device_requirements1, ud.device_requirements2, ud.device_requirements3
            FROM user_devices ud
            WHERE ud.user_id = :user_id";

        // Prepare statement
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);

        // Execute statement
        $stmt->execute();

        // Fetch data
        $result = $stmt->fetchAll();

        if ($result !== false && count($result) > 0) {
            // Jika data ditemukan, kirimkan sebagai respons JSON
            return $response->withJson(["status" => "success", "data" => $result], 200);
        } else {
            // Jika data tidak ditemukan, kirimkan pesan error
            return $response->withJson(["status" => "error", "message" => "User not found"], 404);
        }
    });

    // Route untuk melakukan proses register.
    $app->post("/register", function (Request $request, Response $response) {
        $data = $request->getParsedBody();

        // Mengambil data dari request POST dan melakukan validasi.
        $username = ($data['username'] ?? '');
        $email = ($data['email'] ?? '');
        $password = ($data['password'] ?? '');
        $deviceName = ($data['deviceName'] ?? '');
        $device_requirements1 = ($data['device_requirements1'] ?? '');
        $device_requirements2 = ($data['device_requirements2'] ?? '');
        $device_requirements3 = ($data['device_requirements3'] ?? '');

        // Lakukan validasi data
        if (empty($username) || empty($email) || empty($password) || empty($deviceName) || empty($device_requirements1)) {
            return $response->withJson(['status' => 'error', 'message' => 'Semua field harus diisi'], 400);
        }

        // Lakukan validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $response->withJson(['status' => 'error', 'message' => 'Format email tidak valid'], 400);
        }

        // Masukkan data user ke dalam tabel users
        $sqlUser = "INSERT INTO users (username, email, password, role, access) VALUES (:username, :email, :password, 1, 0)";
        $stmtUser = $this->db->prepare($sqlUser);
        $stmtUser->bindParam(':username', $username);
        $stmtUser->bindParam(':email', $email);
        $stmtUser->bindParam(':password', $password);
        $stmtUser->execute();

        // Ambil ID user yang baru saja dimasukkan
        $userId = $this->db->lastInsertId();

        // Masukkan data perangkat ke dalam tabel user_devices
        $sqlDevice = "INSERT INTO user_devices (user_id, deviceName, device_requirements1, device_requirements2, device_requirements3) VALUES (:userId, :deviceName, :device_requirements1, :device_requirements2, :device_requirements3)";
        $stmtDevice = $this->db->prepare($sqlDevice);
        $stmtDevice->bindParam(':userId', $userId);
        $stmtDevice->bindParam(':deviceName', $deviceName);
        $stmtDevice->bindParam(':device_requirements1', $device_requirements1);
        $stmtDevice->bindParam(':device_requirements2', $device_requirements2);
        $stmtDevice->bindParam(':device_requirements3', $device_requirements3);
        $stmtDevice->execute();

        return $response->withJson(['status' => 'success', 'message' => 'Registrasi berhasil']);
    });
};
