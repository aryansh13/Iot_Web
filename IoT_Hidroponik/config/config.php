<?php 

class database
{
    var $host = "localhost";
    var $username = "root";
    var $password = "";
    var $database = "db_iot";
    var $koneksi = "";
    function __construct()
    {
        $this->koneksi = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        if (mysqli_connect_errno())
        {
            echo "Koneksi database gagal : " . mysqli_connect_error();
        }
    }

    function login($email)
    {
        $data = mysqli_query($this->koneksi,"SELECT * FROM users WHERE email = '$email'");
        if(mysqli_num_rows($data) == 0){
            echo "<b>Data user tidak</b>";
            $hasil = [];
            header('location: ../view/login/login.php');
        }
        else{
            while($row = mysqli_fetch_array($data)){
                $hasil[] = $row;
            }
        }
        return $hasil;
    }

    function registerUser($username, $email, $password){
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 1)";
        mysqli_query($this->koneksi, $query);
    }

    function getUserIdByEmail($email){
        $query = "SELECT id FROM users WHERE email = '$email'";
        $result = mysqli_query($this->koneksi, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }

    function insertUserDevice($userId, $deviceName, $deviceRequirements)
    {
        $query = "INSERT INTO user_devices (user_id, deviceName, device_requirements1, device_requirements2, device_requirements3) VALUES ('$userId', '$deviceName', ?, ?, ?)";
        $stmt = $this->koneksi->prepare($query);

        $requirements = array_slice($deviceRequirements, 0, 3);
        $requirements = array_pad($requirements, 3, null);

        $stmt->bind_param("sss", $requirements[0], $requirements[1], $requirements[2]);
        $stmt->execute();
    }

    function getDeviceRequirementsByUserId($userId)
    {
        $query = "SELECT device_requirements1, device_requirements2, device_requirements3 FROM user_devices WHERE user_id = '$userId'";
        $result = mysqli_query($this->koneksi, $query);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }
}

?>