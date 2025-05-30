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

// Add new columns to users table if they don't exist
$new_columns = [
    'phone' => 'VARCHAR(20)',
    'address' => 'TEXT',
    'education' => 'VARCHAR(100)',
    'major' => 'VARCHAR(100)',
    'work_experience' => 'TEXT',
    'certifications' => 'TEXT',
    'linkedin' => 'VARCHAR(255)',
    'github' => 'VARCHAR(255)',
    'portfolio' => 'VARCHAR(255)',
    'profile_photo' => 'VARCHAR(255)'
];

foreach ($new_columns as $column => $type) {
    $check_column = $conn->query("SHOW COLUMNS FROM users LIKE '$column'");
    if ($check_column->num_rows == 0) {
        $sql = "ALTER TABLE users ADD COLUMN $column $type";
        if (!$conn->query($sql)) {
            die("Error adding column $column: " . $conn->error);
        }
    }
}

// Ambil data pengguna dari database
$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $status = $_POST['status'];
    $skills = $_POST['skills'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $education = $_POST['education'];
    $major = $_POST['major'];
    $work_experience = $_POST['work_experience'];
    $certifications = $_POST['certifications'];
    $linkedin = $_POST['linkedin'];
    $github = $_POST['github'];
    $portfolio = $_POST['portfolio'];
    
    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profile_photos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png');
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = $user_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $profile_photo = $upload_path;
            } else {
                $error = "Gagal mengupload foto profil.";
            }
        } else {
            $error = "Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.";
        }
    }
    
    if (empty($error)) {
        $sql = "UPDATE users SET full_name = ?, email = ?, gender = ?, birthdate = ?, status = ?, skills = ?, 
                phone = ?, address = ?, education = ?, major = ?, work_experience = ?, certifications = ?, 
                linkedin = ?, github = ?, portfolio = ?";
        $params = array($full_name, $email, $gender, $birthdate, $status, $skills, 
                      $phone, $address, $education, $major, $work_experience, $certifications,
                      $linkedin, $github, $portfolio);
        $types = "sssssssssssssss";
        
        if (isset($profile_photo)) {
            $sql .= ", profile_photo = ?";
            $params[] = $profile_photo;
            $types .= "s";
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $message = "Profil berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui profil: " . $conn->error;
        }
        $stmt->close();
    }
}

// Get current user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - GaweYuk</title>
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
        .edit-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        .edit-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px;
        }
        .edit-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .edit-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #222;
            margin-bottom: 8px;
        }
        .photo-upload {
            text-align: center;
            margin-bottom: 32px;
        }
        .current-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 16px;
            overflow: hidden;
            border: 4px solid #2E7D32;
            background: #f4f8fb;
        }
        .current-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .form-label {
            font-weight: 500;
            color: #444;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
        }
        .form-control:focus {
            border-color: #2E7D32;
            box-shadow: 0 0 0 0.2rem rgba(46,125,50,0.25);
        }
        .btn-save {
            background: #2E7D32;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-save:hover {
            background: #1976d2;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-cancel {
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: 0.2s;
            text-decoration: none;
            display: inline-block;
            margin-left: 12px;
        }
        .btn-cancel:hover {
            background: #c82333;
            color: #fff;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 24px;
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

    <div class="edit-container">
        <div class="edit-card">
            <div class="edit-header">
                <h1 class="edit-title">Edit Profil</h1>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="photo-upload">
                    <div class="current-photo">
                        <?php if (!empty($user_data['profile_photo'])): ?>
                            <img src="<?php echo htmlspecialchars($user_data['profile_photo']); ?>" alt="Current Profile Photo">
                        <?php else: ?>
                            <img src="assets/default-profile.png" alt="Default Profile Photo">
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Ubah Foto Profil</label>
                        <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                        <small class="text-muted">Format yang didukung: JPG, JPEG, PNG. Maksimal 2MB.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Jenis Kelamin</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="Laki-laki" <?php echo $user_data['gender'] === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="Perempuan" <?php echo $user_data['gender'] === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="birthdate" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($user_data['birthdate']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Pencari Kerja" <?php echo $user_data['status'] === 'Pencari Kerja' ? 'selected' : ''; ?>>Pencari Kerja</option>
                        <option value="Bekerja" <?php echo $user_data['status'] === 'Bekerja' ? 'selected' : ''; ?>>Bekerja</option>
                        <option value="Mahasiswa" <?php echo $user_data['status'] === 'Mahasiswa' ? 'selected' : ''; ?>>Mahasiswa</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="skills" class="form-label">Keahlian</label>
                    <textarea class="form-control" id="skills" name="skills" rows="4"><?php echo htmlspecialchars($user_data['skills']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="education" class="form-label">Pendidikan Terakhir</label>
                    <input type="text" class="form-control" id="education" name="education" value="<?php echo htmlspecialchars($user_data['education'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="major" class="form-label">Jurusan</label>
                    <input type="text" class="form-control" id="major" name="major" value="<?php echo htmlspecialchars($user_data['major'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="work_experience" class="form-label">Pengalaman Kerja</label>
                    <textarea class="form-control" id="work_experience" name="work_experience" rows="4"><?php echo htmlspecialchars($user_data['work_experience'] ?? ''); ?></textarea>
                    <small class="text-muted">Jelaskan pengalaman kerja Anda secara detail, termasuk posisi, perusahaan, dan tanggung jawab.</small>
                </div>

                <div class="mb-3">
                    <label for="certifications" class="form-label">Sertifikasi</label>
                    <textarea class="form-control" id="certifications" name="certifications" rows="3"><?php echo htmlspecialchars($user_data['certifications'] ?? ''); ?></textarea>
                    <small class="text-muted">Masukkan sertifikasi yang Anda miliki, termasuk nama sertifikasi dan lembaga yang mengeluarkan.</small>
                </div>

                <div class="mb-3">
                    <label for="linkedin" class="form-label">LinkedIn</label>
                    <input type="url" class="form-control" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($user_data['linkedin'] ?? ''); ?>">
                    <small class="text-muted">Masukkan URL profil LinkedIn Anda.</small>
                </div>

                <div class="mb-3">
                    <label for="github" class="form-label">GitHub</label>
                    <input type="url" class="form-control" id="github" name="github" value="<?php echo htmlspecialchars($user_data['github'] ?? ''); ?>">
                    <small class="text-muted">Masukkan URL profil GitHub Anda.</small>
                </div>

                <div class="mb-3">
                    <label for="portfolio" class="form-label">Portfolio</label>
                    <input type="url" class="form-control" id="portfolio" name="portfolio" value="<?php echo htmlspecialchars($user_data['portfolio'] ?? ''); ?>">
                    <small class="text-muted">Masukkan URL portfolio atau website pribadi Anda.</small>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                    <a href="profil.php" class="btn btn-cancel">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </form>
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