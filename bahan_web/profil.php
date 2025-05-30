<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect jika tidak ada sesi
    exit();
}

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

// Ambil data pengguna dari database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc(); // Ambil data pengguna
} else {
    echo "Data pengguna tidak ditemukan.";
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - GaweYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            background: #f4f8fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #222;
        }
        .navbar {
            background-color: #fff !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2E7D32 !important;
        }
        .navbar-brand span {
            color: #222;
        }
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #fff;
            padding-top: 20px;
            transition: 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0,0,0,0.07);
        }
        .sidebar a {
            padding: 12px 20px;
            text-decoration: none;
            font-size: 1rem;
            color: #222;
            display: block;
            transition: 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover {
            background-color: #f4f8fb;
            color: #2E7D32;
            border-left: 3px solid #2E7D32;
        }
        .logout-btn {
            margin: 20px 15px;
            padding: 12px;
            text-align: center;
            font-size: 1rem;
            color: white;
            background-color: #dc3545;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .logout-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .open-sidebar-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1500;
            color: #2E7D32;
        }
        .active-sidebar {
            left: 0;
        }
        .profile-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }
        .profile-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .profile-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #222;
            margin-bottom: 8px;
        }
        .profile-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 24px;
            overflow: hidden;
            border: 4px solid #2E7D32;
            background: #f4f8fb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 32px;
        }
        .info-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 24px;
        }
        .info-section h3 {
            color: #2E7D32;
            font-size: 1.2rem;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #2E7D32;
        }
        .info-item {
            margin-bottom: 16px;
        }
        .info-label {
            font-weight: 600;
            color: #444;
            margin-bottom: 4px;
        }
        .info-value {
            color: #666;
            background: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        .btn-edit {
            background: #2E7D32;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: 0.2s;
            text-decoration: none;
            display: inline-block;
            margin-top: 24px;
        }
        .btn-edit:hover {
            background: #1976d2;
            color: #fff;
            transform: translateY(-2px);
        }
        .social-link {
            color: #2E7D32;
            text-decoration: none;
            transition: 0.2s;
        }
        .social-link:hover {
            color: #1976d2;
            text-decoration: underline;
        }
        .text-muted {
            color: #6c757d;
            font-style: italic;
        }
        .info-value a {
            word-break: break-all;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-bars"></i>
                <span>Gawe</span>Yuk
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="tentang_kami.php">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="lowongan.php">Lowongan Kerja</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="tambah_lowongan.php">Posting Pekerjaan</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="mySidebar" class="sidebar">
        <h4 class="text-center mb-4"><?php echo $_SESSION['full_name']; ?></h4>
        <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
        <a href="profil.php"><i class="fas fa-user me-2"></i>Profil</a>
        <a href="tentang_kami.php"><i class="fas fa-info-circle me-2"></i>Tentang Kami</a>
        <a href="kirim_masukan.php"><i class="fas fa-comment me-2"></i>Kirim Masukan</a>
        <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
    </div>

    <span class="open-sidebar-btn" onclick="toggleSidebar()">☰</span>

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h1 class="profile-title">Profil Saya</h1>
                <div class="profile-photo">
                    <?php if (!empty($user_data['profile_photo'])): ?>
                        <img src="<?php echo htmlspecialchars($user_data['profile_photo']); ?>" alt="Profile Photo">
                    <?php else: ?>
                        <img src="assets/default-profile.png" alt="Default Profile Photo">
                    <?php endif; ?>
                </div>
                <a href="edit_profile.php" class="btn btn-edit">
                    <i class="fas fa-edit me-2"></i>Edit Profil
                </a>
            </div>

            <div class="profile-info">
                <div class="info-section">
                    <h3><i class="fas fa-user me-2"></i>Informasi Pribadi</h3>
                    <div class="info-item">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['full_name']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jenis Kelamin</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['gender']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Lahir</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['birthdate']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nomor Telepon</div>
                        <div class="info-value"><?php echo !empty($user_data['phone']) ? htmlspecialchars($user_data['phone']) : '<span class="text-muted">Belum ditambahkan</span>'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Alamat</div>
                        <div class="info-value"><?php echo !empty($user_data['address']) ? htmlspecialchars($user_data['address']) : '<span class="text-muted">Belum ditambahkan</span>'; ?></div>
                    </div>
                </div>

                <div class="info-section">
                    <h3><i class="fas fa-briefcase me-2"></i>Informasi Profesional</h3>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['status']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Pendidikan Terakhir</div>
                        <div class="info-value"><?php echo !empty($user_data['education']) ? htmlspecialchars($user_data['education']) : '<span class="text-muted">Belum ditambahkan</span>'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jurusan</div>
                        <div class="info-value"><?php echo !empty($user_data['major']) ? htmlspecialchars($user_data['major']) : '<span class="text-muted">Belum ditambahkan</span>'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Pengalaman Kerja</div>
                        <div class="info-value"><?php echo !empty($user_data['work_experience']) ? nl2br(htmlspecialchars($user_data['work_experience'])) : '<span class="text-muted">Belum ditambahkan</span>'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Keahlian</div>
                        <div class="info-value"><?php echo nl2br(htmlspecialchars($user_data['skills'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Sertifikasi</div>
                        <div class="info-value"><?php echo !empty($user_data['certifications']) ? nl2br(htmlspecialchars($user_data['certifications'])) : '<span class="text-muted">Belum ditambahkan</span>'; ?></div>
                    </div>
                </div>

                <div class="info-section">
                    <h3><i class="fas fa-link me-2"></i>Media Sosial & Portfolio</h3>
                    <div class="info-item">
                        <div class="info-label">LinkedIn</div>
                        <div class="info-value">
                            <?php if (!empty($user_data['linkedin'])): ?>
                                <a href="<?php echo htmlspecialchars($user_data['linkedin']); ?>" target="_blank" class="social-link">
                                    <i class="fab fa-linkedin me-2"></i><?php echo htmlspecialchars($user_data['linkedin']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Belum ditambahkan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">GitHub</div>
                        <div class="info-value">
                            <?php if (!empty($user_data['github'])): ?>
                                <a href="<?php echo htmlspecialchars($user_data['github']); ?>" target="_blank" class="social-link">
                                    <i class="fab fa-github me-2"></i><?php echo htmlspecialchars($user_data['github']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Belum ditambahkan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Portfolio</div>
                        <div class="info-value">
                            <?php if (!empty($user_data['portfolio'])): ?>
                                <a href="<?php echo htmlspecialchars($user_data['portfolio']); ?>" target="_blank" class="social-link">
                                    <i class="fas fa-globe me-2"></i><?php echo htmlspecialchars($user_data['portfolio']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Belum ditambahkan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("mySidebar");
            sidebar.classList.toggle("active-sidebar");
        }
        
        function logout() {
            alert("Anda telah logout!");
            window.location.href = "index.php";
        }
    </script>
</body>
</html>
