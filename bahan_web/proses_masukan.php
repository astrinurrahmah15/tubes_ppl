<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect jika tidak ada sesi
    exit();
}

// Konfigurasi database
$host = "localhost";
$username = "root";
$password = "";
$database = "gawe_yuk";

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses form jika data dikirim
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ambil data dari form
    $nama = trim($_POST["nama"]);
    $nomor_hp = trim($_POST["nomor_hp"]);
    $email = trim($_POST["email"]);
    $jenis_kelamin = trim($_POST["jenis_kelamin"]);
    $masukan = trim($_POST["masukan"]);

    // Validasi input
    if (empty($nama) || empty($nomor_hp) || empty($email) || empty($jenis_kelamin) || empty($masukan)) {
        echo "<div class='alert alert-danger'>Semua kolom harus diisi.</div>";
    } else {
        // Simpan data ke database
        $sql = "INSERT INTO masukan (nama, nomor_hp, email, jenis_kelamin, masukan) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nama, $nomor_hp, $email, $jenis_kelamin, $masukan);

        if ($stmt->execute()) {
            echo "Masukan berhasil dikirim!";
            header("Location: kirim_masukan.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Gagal mengirim masukan: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

$conn->close();
?>