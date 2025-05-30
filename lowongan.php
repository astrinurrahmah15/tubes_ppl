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

// Ambil data lowongan dari database
$sql = "SELECT * FROM lowongan ORDER BY created_at DESC";
$result = $conn->query($sql);

$lowongan = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if user has already applied
        $check_sql = "SELECT id FROM lamaran WHERE user_id = ? AND lowongan_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $_SESSION['user_id'], $row['id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row['has_applied'] = $check_result->num_rows > 0;
        $check_stmt->close();
        
        $lowongan[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Lowongan Kerja - GaweYuk</title>
    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
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
        .main-content {
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .search-filter-box {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 24px 24px 8px 24px;
            margin-bottom: 32px;
        }
        .job-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
        }
        .job-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 24px;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
            position: relative;
        }
        .job-card:hover {
            box-shadow: 0 8px 24px rgba(46,125,50,0.10);
            transform: translateY(-4px) scale(1.01);
        }
        .job-logo {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: #f4f8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #2E7D32;
            margin-bottom: 12px;
        }
        .job-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 4px;
        }
        .company-name {
            color: #2E7D32;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .job-meta {
            font-size: 0.97rem;
            color: #555;
            margin-bottom: 8px;
        }
        .job-meta i {
            margin-right: 6px;
        }
        .job-desc {
            color: #444;
            font-size: 0.97rem;
            margin-bottom: 12px;
            min-height: 48px;
        }
        .job-badges {
            margin-bottom: 12px;
        }
        .job-badge {
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 12px;
            padding: 4px 14px;
            font-size: 0.85rem;
            margin-right: 6px;
            margin-bottom: 4px;
            display: inline-block;
        }
        .job-date {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 8px;
        }
        .btn-detail {
            background: #2E7D32;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 22px;
            font-weight: 600;
            align-self: flex-end;
            transition: 0.2s;
        }
        .btn-detail:hover {
            background: #1976d2;
            color: #fff;
        }
        .no-jobs {
            text-align: center;
            color: #888;
            padding: 60px 0;
        }
        .footer {
            background-color: #2E7D32;
            color: white;
            padding: 40px 0;
            margin-top: auto;
        }
        .footer h5 {
            font-weight: 600;
            margin-bottom: 20px;
        }
        .footer a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: 0.3s;
        }
        .footer a:hover {
            opacity: 1;
            text-decoration: none;
        }
        @media (max-width: 991px) {
            .job-grid {
                grid-template-columns: 1fr;
            }
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
                <li class="nav-item"><a class="nav-link active" href="lowongan.php">Lowongan Kerja</a></li>
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
<div class="container main-content">
    <div class="search-filter-box mb-4">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Cari Lowongan</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Posisi, perusahaan, atau kata kunci" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            <div class="col-md-3">
                <label for="lokasi" class="form-label">Lokasi</label>
                <select class="form-select" id="lokasi" name="lokasi">
                    <option value="">Semua Lokasi</option>
                    <option value="Jakarta" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Jakarta') echo 'selected'; ?>>Jakarta</option>
                    <option value="Surabaya" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Surabaya') echo 'selected'; ?>>Surabaya</option>
                    <option value="Bandung" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Bandung') echo 'selected'; ?>>Bandung</option>
                    <option value="Medan" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Medan') echo 'selected'; ?>>Medan</option>
                    <option value="Semarang" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Semarang') echo 'selected'; ?>>Semarang</option>
                    <option value="Makassar" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Makassar') echo 'selected'; ?>>Makassar</option>
                    <option value="Palembang" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Palembang') echo 'selected'; ?>>Palembang</option>
                    <option value="Depok" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Depok') echo 'selected'; ?>>Depok</option>
                    <option value="Tangerang" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Tangerang') echo 'selected'; ?>>Tangerang</option>
                    <option value="Bekasi" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Bekasi') echo 'selected'; ?>>Bekasi</option>
                    <option value="Bogor" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Bogor') echo 'selected'; ?>>Bogor</option>
                    <option value="Malang" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Malang') echo 'selected'; ?>>Malang</option>
                    <option value="Yogyakarta" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Yogyakarta') echo 'selected'; ?>>Yogyakarta</option>
                    <option value="Denpasar" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Denpasar') echo 'selected'; ?>>Denpasar</option>
                    <option value="Batam" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Batam') echo 'selected'; ?>>Batam</option>
                    <option value="Pekanbaru" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Pekanbaru') echo 'selected'; ?>>Pekanbaru</option>
                    <option value="Padang" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Padang') echo 'selected'; ?>>Padang</option>
                    <option value="Balikpapan" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Balikpapan') echo 'selected'; ?>>Balikpapan</option>
                    <option value="Manado" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Manado') echo 'selected'; ?>>Manado</option>
                    <option value="Pontianak" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Pontianak') echo 'selected'; ?>>Pontianak</option>
                    <option value="Banjarmasin" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Banjarmasin') echo 'selected'; ?>>Banjarmasin</option>
                    <option value="Jambi" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Jambi') echo 'selected'; ?>>Jambi</option>
                    <option value="Mataram" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Mataram') echo 'selected'; ?>>Mataram</option>
                    <option value="Kupang" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Kupang') echo 'selected'; ?>>Kupang</option>
                    <option value="Jayapura" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Jayapura') echo 'selected'; ?>>Jayapura</option>
                    <option value="Palu" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Palu') echo 'selected'; ?>>Palu</option>
                    <option value="Ambon" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Ambon') echo 'selected'; ?>>Ambon</option>
                    <option value="Ternate" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Ternate') echo 'selected'; ?>>Ternate</option>
                    <option value="Banda Aceh" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Banda Aceh') echo 'selected'; ?>>Banda Aceh</option>
                    <option value="Pangkal Pinang" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Pangkal Pinang') echo 'selected'; ?>>Pangkal Pinang</option>
                    <option value="Tanjung Pinang" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Tanjung Pinang') echo 'selected'; ?>>Tanjung Pinang</option>
                    <option value="Kendari" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Kendari') echo 'selected'; ?>>Kendari</option>
                    <option value="Gorontalo" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Gorontalo') echo 'selected'; ?>>Gorontalo</option>
                    <option value="Mamuju" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Mamuju') echo 'selected'; ?>>Mamuju</option>
                    <option value="Sofifi" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Sofifi') echo 'selected'; ?>>Sofifi</option>
                    <option value="Manokwari" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Manokwari') echo 'selected'; ?>>Manokwari</option>
                    <option value="Merauke" <?php if(isset($_GET['lokasi']) && $_GET['lokasi']==='Merauke') echo 'selected'; ?>>Merauke</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="jenis" class="form-label">Jenis Pekerjaan</label>
                <select class="form-select" id="jenis" name="jenis">
                    <option value="">Semua</option>
                    <option value="Full-time" <?php if(isset($_GET['jenis']) && $_GET['jenis']==='Full-time') echo 'selected'; ?>>Full-time</option>
                    <option value="Part-time" <?php if(isset($_GET['jenis']) && $_GET['jenis']==='Part-time') echo 'selected'; ?>>Part-time</option>
                    <option value="Freelance" <?php if(isset($_GET['jenis']) && $_GET['jenis']==='Freelance') echo 'selected'; ?>>Freelance</option>
                    <option value="Internship" <?php if(isset($_GET['jenis']) && $_GET['jenis']==='Internship') echo 'selected'; ?>>Internship</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-success"><i class="fas fa-search me-2"></i>Cari</button>
            </div>
        </form>
    </div>
    <?php
    // Filter logic
    $filtered = $lowongan;
    if (!empty($_GET['search'])) {
        $filtered = array_filter($filtered, function($l) {
            $q = strtolower($_GET['search']);
            return strpos(strtolower($l['jenis_pekerjaan']), $q) !== false || strpos(strtolower($l['nama_perusahaan']), $q) !== false || strpos(strtolower($l['deskripsi']), $q) !== false;
        });
    }
    if (!empty($_GET['lokasi'])) {
        $filtered = array_filter($filtered, function($l) {
            return strpos(strtolower($l['alamat']), strtolower($_GET['lokasi'])) !== false;
        });
    }
    if (!empty($_GET['jenis'])) {
        $filtered = array_filter($filtered, function($l) {
            return strtolower($l['jenis_pekerjaan']) === strtolower($_GET['jenis']);
        });
    }
    ?>
    <?php if (count($filtered) === 0): ?>
        <div class="no-jobs">Tidak ada lowongan ditemukan.</div>
    <?php else: ?>
        <div class="job-grid">
            <?php foreach ($filtered as $loker): ?>
                <div class="job-card">
                    <div class="job-logo">
                        <?php if (!empty($loker['gambar'])): ?>
                            <img src="<?php echo htmlspecialchars($loker['gambar']); ?>" alt="Logo" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                        <?php else: ?>
                            <i class="fas fa-building"></i>
                        <?php endif; ?>
                    </div>
                    <div class="job-title"><?php echo htmlspecialchars($loker['jenis_pekerjaan']); ?></div>
                    <div class="company-name"><?php echo htmlspecialchars($loker['nama_perusahaan']); ?></div>
                    <div class="job-meta mb-2">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($loker['alamat']); ?></span>
                        <span class="ms-3"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($loker['gaji']); ?></span>
                    </div>
                    <div class="job-badges">
                        <span class="job-badge"><?php echo htmlspecialchars($loker['jenis_pekerjaan']); ?></span>
                        <?php if (!empty($loker['tunjangan'])): ?><span class="job-badge">Tunjangan</span><?php endif; ?>
                    </div>
                    <div class="job-desc"><?php echo htmlspecialchars(mb_strimwidth($loker['deskripsi'], 0, 120, '...')); ?></div>
                    <div class="job-date mb-2"><i class="fas fa-calendar-alt"></i> <?php echo isset($loker['created_at']) ? date('d M Y', strtotime($loker['created_at'])) : '-'; ?></div>
                    <div class="d-flex gap-2">
                        <a href="detail_lowongan.php?id=<?php echo $loker['id']; ?>" class="btn btn-detail">Detail Lowongan</a>
                        <?php if (!$loker['has_applied']): ?>
                            <a href="detail_lowongan.php?id=<?php echo $loker['id']; ?>#application-form" class="btn btn-success">Lamar Sekarang</a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Sudah Dilamar</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<footer class="footer mt-5" style="border-top:1px solid #e0e0e0;">
    <div class="container py-4">
        <div class="row gy-4">
            <div class="col-12 col-md-3">
                <h5 class="mb-3">Tentang GaweYuk</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#">Tentang kami</a></li>
                    <li class="mb-2"><a href="#">Kirim saran</a></li>
                    <li><a href="#">Partner kami</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-3">
                <h5 class="mb-3">Pencari kerja</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#">FAQ</a></li>
                    <li class="mb-2"><a href="#">Cari pekerjaan</a></li>
                    <li><a href="#">Testimoni</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-3">
                <h5 class="mb-3">Perusahaan</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="#">Posting lowongan</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-3">
                <h5 class="mb-3">Media Sosial</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#"><i class="fab fa-facebook me-2"></i>Facebook</a></li>
                    <li><a href="#"><i class="fab fa-instagram me-2"></i>Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col text-center text-white-50 small" style="opacity:0.7;">
                &copy; <?php echo date('Y'); ?> GaweYuk. All rights reserved.
            </div>
        </div>
    </div>
</footer>
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

#
