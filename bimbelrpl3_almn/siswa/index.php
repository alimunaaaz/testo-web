<?php
/**
 * File: siswa/index.php
 * Fungsi: Dashboard siswa
 */

require_once '../functions/functions.php';
check_siswa(); // Cek apakah user adalah siswa

$base_url = '../';
$page_title = 'Dashboard Siswa';

// Get statistik siswa ini
$stats = get_statistik_siswa($_SESSION['user_id']);

// Get riwayat 5 aspirasi terbaru siswa ini
$aspirasi_terbaru = get_all_aspirasi(['id_user' => $_SESSION['user_id']]);
$aspirasi_terbaru = array_slice($aspirasi_terbaru, 0, 5);

// Get semua kategori
$kategoris = get_all_kategori();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-home"></i> Dashboard Siswa</h2>
            <p class="text-muted">Selamat datang, <strong><?php echo $_SESSION['nama']; ?></strong> - <?php echo $_SESSION['kelas']; ?></p>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <!-- Card Total -->
        <div class="col-md-3">
            <div class="card stat-card total">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Aspirasi Saya</h6>
                            <h2 class="mb-0"><?php echo $stats['total_aspirasi']; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-clipboard-list fa-3x text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Pending -->
        <div class="col-md-3">
            <div class="card stat-card pending">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Menunggu</h6>
                            <h2 class="mb-0"><?php echo $stats['pending']; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-3x text-warning opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Proses -->
        <div class="col-md-3">
            <div class="card stat-card proses">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Diproses</h6>
                            <h2 class="mb-0"><?php echo $stats['proses']; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-spinner fa-3x text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Selesai -->
        <div class="col-md-3">
            <div class="card stat-card selesai">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Selesai</h6>
                            <h2 class="mb-0"><?php echo $stats['selesai']; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Penggunaan -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Cara Menggunakan</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li class="mb-2">
                            Klik menu <strong>"Buat Aspirasi"</strong> untuk membuat pengaduan baru
                        </li>
                        <li class="mb-2">
                            Isi formulir dengan lengkap dan jelas
                        </li>
                        <li class="mb-2">
                            Upload foto pendukung jika ada (opsional)
                        </li>
                        <li class="mb-2">
                            Pantau status di menu <strong>"Aspirasi Saya"</strong>
                        </li>
                        <li class="mb-2">
                            Lihat tanggapan admin di halaman detail
                        </li>
                    </ol>

                    <div class="alert alert-warning mb-0">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Penting:</strong> Pastikan informasi yang Anda berikan akurat dan jelas!
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategori Aspirasi -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-tags"></i> Kategori Tersedia</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($kategoris as $kat): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tag text-secondary me-2"></i>
                                    <span><?php echo $kat['nama_kategori']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Aspirasi Terbaru -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Aspirasi Terbaru</h5>
            <a href="aspirasi_saya.php" class="btn btn-sm btn-primary">
                Lihat Semua <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($aspirasi_terbaru)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Anda belum memiliki aspirasi</p>
                    <a href="form_aspirasi.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Buat Aspirasi Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aspirasi_terbaru as $asp): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($asp['tanggal_pengaduan'])); ?></td>
                                <td><?php echo $asp['judul']; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $asp['nama_kategori']; ?></span></td>
                                <td>
                                    <?php
                                    $status_class = [
                                        'pending' => 'warning',
                                        'proses' => 'info',
                                        'selesai' => 'success',
                                        'ditolak' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $status_class[$asp['status']]; ?>">
                                        <?php echo strtoupper($asp['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail_aspirasi.php?id=<?php echo $asp['id_aspirasi']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>