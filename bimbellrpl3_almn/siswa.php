<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("location:index.php");
    exit();
}
// ... sisa kode lainnya

// 2. Definisi NIS (WAJIB di taruh paling atas setelah session)
$nis_login = $_SESSION['nis'];

// 3. Logika Update Profil
if (isset($_POST['update_siswa'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_siswa']);
    $pw = mysqli_real_escape_string($koneksi, $_POST['pw_siswa']);

    // Pastikan variabel $nis_login sudah ada sebelum query ini dijalankan
    $query = mysqli_query($koneksi, "UPDATE siswa SET nama_siswa='$nama', password='$pw' WHERE nis='$nis_login'");
    if ($query) {
        $_SESSION['nama'] = $nama;
        echo "<script>alert('Profil Berhasil Diperbarui!'); window.location='siswa.php';</script>";
    }
}
// ... sisa kode lainnya ...

$nis_login = $_SESSION['nis'];

// Logika Kirim Aspirasi
if (isset($_POST['kirim'])) {
    $kat = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $lok = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $ket = mysqli_real_escape_string($koneksi, $_POST['ket']);

    $query = mysqli_query($koneksi, "INSERT INTO Input_Aspirasi (nis, id_kategori, lokasi, ket) VALUES ('$nis_login', '$kat', '$lok', '$ket')");

    if ($query) {
        echo "<script>alert('Aspirasi berhasil dikirim!'); window.location='siswa.php';</script>";
    }
}

// Statistik Pribadi Siswa
$total_saya = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM Input_Aspirasi WHERE nis='$nis_login'"));
$selesai_saya = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM Input_Aspirasi i JOIN Aspirasi a ON i.id_pelaporan=a.id_pelaporan WHERE i.nis='$nis_login' AND a.status='Selesai'"));
// --- LOGIKA UPDATE PROFIL SISWA (PASTE DI SINI) ---

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Student Portal | E-Aspirasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --std-blue: #0d6efd;
            --std-dark: #212529;
        }

        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .navbar-student {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%);
            color: white;
            padding: 40px 0;
            border-radius: 0 0 40px 40px;
            margin-bottom: 30px;
        }

        .card-glass {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card-glass:hover {
            transform: translateY(-5px);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #e0e0e0;
        }

        .btn-send {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .status-badge {
            border-radius: 30px;
            padding: 5px 15px;
            font-size: 0.8rem;
        }

        .timeline-item {
            border-left: 3px solid #0d6efd;
            padding-left: 20px;
            margin-bottom: 20px;
            position: relative;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-student sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#"><i class="fas fa-graduation-cap me-2"></i>ASPPIRASI PELAJAR</a>
            <div class="d-flex align-items-center">
                <span class="me-3 d-none d-md-block text-muted">Halo, <strong>NIS: <?php echo $nis_login; ?></strong></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
            </div>
        </div>
    </nav>

    <div class="hero-section shadow-sm">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nama'] ?? $nis_login); ?>&background=random&size=128" class="rounded-circle border border-4 border-white shadow">
                </div>
                <div class="col-md-6" data-aos="fade-right">
                    <h1 class="fw-bold">Halo, Pelajar!</h1>
                    <p class="lead">NIS: <?php echo $nis_login; ?> | Kelola profil dan laporanmu di sini.</p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block" data-aos="fade-left">
                    <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalProfil">
                        <i class="fas fa-user-edit me-2"></i> Edit Profil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card card-glass p-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-pen-nib me-2"></i>Buat Laporan Baru</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kategori Layanan</label>
                            <select name="id_kategori" class="form-select shadow-sm" required>
                                <option value="">Pilih Kategori...</option>
                                <?php
                                $kat = mysqli_query($koneksi, "SELECT * FROM Kategori");
                                while ($rk = mysqli_fetch_array($kat)) {
                                    echo "<option value='$rk[id_kategori]'>$rk[ket_kategori]</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Lokasi Kejadian</label>
                            <input type="text" name="lokasi" class="form-control shadow-sm" placeholder="Contoh: Lab RPL, Kantin" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Detail Aspirasi</label>
                            <textarea name="ket" class="form-control shadow-sm" rows="4" placeholder="Ceritakan detail masalah atau saranmu..." required></textarea>
                        </div>
                        <button type="submit" name="kirim" class="btn btn-primary btn-send w-100 shadow">
                            KIRIM LAPORAN <i class="fas fa-paper-plane ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                <div class="card card-glass p-4 min-height-100">
                    <h5 class="fw-bold mb-4"><i class="fas fa-history me-2 text-primary"></i>Riwayat Aspirasi Saya</h5>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr class="text-muted small">
                                    <th>DETAIL LAPORAN</th>
                                    <th>STATUS</th>
                                    <th>TANGGAPAN ADMIN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = mysqli_query($koneksi, "SELECT i.*, a.status, a.feedback FROM Input_Aspirasi i LEFT JOIN Aspirasi a ON i.id_pelaporan=a.id_pelaporan WHERE i.nis='$nis_login' ORDER BY i.id_pelaporan DESC");

                                if (mysqli_num_rows($q) == 0) {
                                    echo "<tr><td colspan='3' class='text-center py-5 text-muted'>Belum ada aspirasi yang dikirim.</td></tr>";
                                }

                                while ($r = mysqli_fetch_array($q)) {
                                    $st = $r['status'] ?? 'Menunggu';
                                    $badge = ($st == 'Selesai') ? 'bg-success' : (($st == 'Proses') ? 'bg-warning' : 'bg-danger');
                                ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo $r['lokasi']; ?></div>
                                            <div class="small text-muted"><?php echo $r['ket']; ?></div>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badge; ?> status-badge"><?php echo $st; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($r['feedback']) { ?>
                                                <div class="p-2 bg-light rounded small border-start border-primary border-3">
                                                    <i class="fas fa-comment-dots text-primary me-1"></i> <?php echo $r['feedback']; ?>
                                                </div>
                                            <?php } else { ?>
                                                <span class="text-muted small italic">Belum ada tanggapan</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <div class="modal fade" id="modalProfil" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" class="modal-content border-0 rounded-4">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5>Update Data Diri</h5>
                </div>
                <div class="modal-body p-4">
                    <input type="text" name="nama_siswa" class="form-control rounded-pill mb-3" placeholder="Nama Lengkap" required>
                    <input type="password" name="pw_siswa" class="form-control rounded-pill" placeholder="Password Baru" required>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" name="update_siswa" class="btn btn-primary w-100 rounded-pill py-2">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    <?php if (empty($_SESSION['nama'])) : ?>
        <script>
            // Munculkan modal otomatis jika session nama masih kosong
            window.addEventListener('load', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalProfil'));
                myModal.show();
            });
        </script>
    <?php endif; ?>
</body>

</html>