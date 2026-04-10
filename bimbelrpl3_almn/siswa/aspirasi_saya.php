<?php
/**
 * File: siswa/aspirasi_saya.php
 * Fungsi: Daftar semua aspirasi siswa dengan filter status
 */

require_once '../functions/functions.php';
check_siswa();

$base_url = '../';
$page_title = 'Aspirasi Saya';

// Ambil filter status dari GET
$filters = ['id_user' => $_SESSION['user_id']];

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}

// Get aspirasi siswa ini
$aspirasi_list = get_all_aspirasi($filters);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-clipboard-list"></i> Aspirasi Saya</h2>
            <p class="text-muted">Daftar semua pengaduan yang Anda buat</p>
        </div>
        <div class="col-auto">
            <a href="form_aspirasi.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Buat Aspirasi Baru
            </a>
        </div>
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> 
            Aspirasi berhasil dibuat! Admin akan segera menindaklanjuti.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Status -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="btn-group w-100" role="group">
                <a href="aspirasi_saya.php" 
                   class="btn btn-outline-primary <?php echo !isset($_GET['status']) ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> Semua
                </a>
                <a href="aspirasi_saya.php?status=pending" 
                   class="btn btn-outline-warning <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="aspirasi_saya.php?status=proses" 
                   class="btn btn-outline-info <?php echo (isset($_GET['status']) && $_GET['status'] == 'proses') ? 'active' : ''; ?>">
                    <i class="fas fa-spinner"></i> Diproses
                </a>
                <a href="aspirasi_saya.php?status=selesai" 
                   class="btn btn-outline-success <?php echo (isset($_GET['status']) && $_GET['status'] == 'selesai') ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i> Selesai
                </a>
                <a href="aspirasi_saya.php?status=ditolak" 
                   class="btn btn-outline-danger <?php echo (isset($_GET['status']) && $_GET['status'] == 'ditolak') ? 'active' : ''; ?>">
                    <i class="fas fa-times-circle"></i> Ditolak
                </a>
            </div>
        </div>
    </div>

    <!-- Hasil -->
    <div class="mb-3">
        <strong>Menampilkan: <?php echo count($aspirasi_list); ?> Aspirasi</strong>
    </div>

    <?php if (empty($aspirasi_list)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada aspirasi</h5>
                <p class="text-muted">Silakan buat aspirasi baru untuk melaporkan masalah</p>
                <a href="form_aspirasi.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Buat Aspirasi Pertama
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Grid Aspirasi -->
        <div class="row g-4">
            <?php foreach ($aspirasi_list as $asp): ?>
                <div class="col-md-6">
                    <div class="card h-100 hover-shadow">
                        <?php if ($asp['foto']): ?>
                            <img src="../assets/img/uploads/<?php echo $asp['foto']; ?>" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;"
                                 alt="Foto Aspirasi">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary"><?php echo $asp['nama_kategori']; ?></span>
                                <?php
                                $prioritas_class = [
                                    'rendah' => 'secondary',
                                    'sedang' => 'warning',
                                    'tinggi' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?php echo $prioritas_class[$asp['prioritas']]; ?>">
                                    <?php echo strtoupper($asp['prioritas']); ?>
                                </span>
                            </div>

                            <h5 class="card-title"><?php echo $asp['judul']; ?></h5>
                            
                            <p class="card-text text-muted small">
                                <?php 
                                $desc = $asp['deskripsi'];
                                echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc; 
                                ?>
                            </p>

                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo $asp['lokasi']; ?>
                                </small>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo date('d F Y', strtotime($asp['tanggal_pengaduan'])); ?>
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <?php
                                $status_class = [
                                    'pending' => 'warning',
                                    'proses' => 'info',
                                    'selesai' => 'success',
                                    'ditolak' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?php echo $status_class[$asp['status']]; ?> fs-6">
                                    <?php echo strtoupper($asp['status']); ?>
                                </span>

                                <a href="detail_aspirasi.php?id=<?php echo $asp['id_aspirasi']; ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .hover-shadow {
        transition: all 0.3s;
    }
    .hover-shadow:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        transform: translateY(-5px);
    }
</style>

<?php include '../includes/footer.php'; ?>