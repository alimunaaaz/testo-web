<?php
/**
 * File: admin/index.php
 * Fungsi: Dashboard utama admin
 */

require_once '../functions/functions.php';
check_admin(); // Cek apakah user adalah admin

$base_url = '../';
$page_title = 'Dashboard Admin';

// Get statistik
$stats = get_statistik();

// Get aspirasi terbaru (5 terakhir)
$aspirasi_terbaru = get_all_aspirasi();
$aspirasi_terbaru = array_slice($aspirasi_terbaru, 0, 5);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h2>
            <p class="text-muted">Selamat datang, <?php echo $_SESSION['nama']; ?></p>
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
                            <h6 class="text-muted mb-2">Total Aspirasi</h6>
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
                            <h6 class="text-muted mb-2">Pending</h6>
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
                            <h6 class="text-muted mb-2">Dalam Proses</h6>
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

    <!-- Aspirasi Terbaru -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Aspirasi Terbaru</h5>
        </div>
        <div class="card-body">
            <?php if (empty($aspirasi_terbaru)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada aspirasi</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aspirasi_terbaru as $index => $asp): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($asp['tanggal_pengaduan'])); ?></td>
                                <td>
                                    <?php echo $asp['nama_siswa']; ?><br>
                                    <small class="text-muted"><?php echo $asp['kelas']; ?></small>
                                </td>
                                <td><?php echo $asp['judul']; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $asp['nama_kategori']; ?></span></td>
                                <td>
                                    <span class="badge badge-status-<?php echo $asp['status']; ?>">
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
                
                <div class="text-center mt-3">
                    <a href="aspirasi_list.php" class="btn btn-primary">
                        Lihat Semua Aspirasi <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>