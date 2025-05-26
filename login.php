<?php
session_start(); // Memulai sesi

$host = "localhost";
$username = "root";
$password = "";
$database = "gawe_yuk";

// Membuat koneksi ke database menggunakan MySQLi
$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Periksa apakah email dan password dikirim melalui POST
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Gunakan prepared statement untuk mencegah SQL injection
    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id']; // Simpan ID pengguna di sesi
        $_SESSION['full_name'] = $row['full_name']; // Simpan nama pengguna di sesi
        header('Location: dashboard.php'); 
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Login gagal!']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email atau password tidak dikirim!']);
}

// Jika sesi ada, kirimkan full_name pengguna ke frontend
if (isset($_SESSION['full_name'])) {
    echo json_encode(["full_name" => $_SESSION['full_name']]);
} else {
    // Jika sesi tidak ditemukan, kembalikan pesan error
    echo json_encode(["full_name" => "Pengguna Tidak Ditemukan"]);
}

$conn->close();
?>