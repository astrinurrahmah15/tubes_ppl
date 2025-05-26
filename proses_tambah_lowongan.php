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
    $nama_perusahaan = trim($_POST["nama_perusahaan"]);
    $alamat = trim($_POST["alamat"]);
    $gaji = trim($_POST["gaji"]);
    $jenis_pekerjaan = trim($_POST["jenis_pekerjaan"]);
    $tunjangan = trim($_POST["tunjangan"]);
    $umur = trim($_POST["umur"]);
    $email = trim($_POST["email"]);
    $whatsapp = trim($_POST["whatsapp"]);
    $instagram = trim($_POST["instagram"]);
    $facebook = trim($_POST["facebook"]);
    $website = trim($_POST["website"]);
    $deskripsi = trim($_POST["deskripsi"]);

    // Validasi input
    if (empty($nama_perusahaan) || empty($alamat) || empty($gaji) || empty($jenis_pekerjaan) || empty($tunjangan) || empty($umur) || empty($email) || empty($whatsapp) || empty($deskripsi)) {
        echo "<div class='alert alert-danger'>Semua kolom wajib diisi.</div>";
    } else {
        // Simpan data ke database
        $sql = "INSERT INTO lowongan (nama_perusahaan, alamat, gaji, jenis_pekerjaan, tunjangan, umur, email, whatsapp, instagram, facebook, website, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $nama_perusahaan, $alamat, $gaji, $jenis_pekerjaan, $tunjangan, $umur, $email, $whatsapp, $instagram, $facebook, $website, $deskripsi);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Lowongan berhasil ditambahkan!</div>";
            // Redirect ke halaman lowongan setelah berhasil
            header("Location: lowongan.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Gagal menambahkan lowongan: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

$conn->close();
?>