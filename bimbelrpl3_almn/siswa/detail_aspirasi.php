<?php
/**
 * File: siswa/detail_aspirasi.php
 * Fungsi: Halaman detail aspirasi untuk siswa (read-only)
 */

require_once '../functions/functions.php';
check_siswa();

$base_url = '../';
$page_title = 'Detail Aspirasi';

// Cek apakah ada ID aspirasi
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: aspirasi_saya.php");
    exit();
}

$id_aspirasi = $_GET['id'];

// Ambil data aspirasi
$aspirasi = get_aspirasi_by_id($id_aspirasi);

// Validasi: aspirasi harus milik siswa ini
if (!$aspirasi || $aspirasi['id_user'] != $_SESSION['user_id']) {
    header("Location: aspirasi_saya.php");
    exit();
}

// Ambil umpan balik
$umpan_balik = get_umpan_balik($id_aspirasi);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <a href="aspirasi_saya.php" class="btn btn-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h2><i class="fas fa-file-alt"></i> Detail Aspirasi</h2>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Utama -->
        <div class="col-md-8">
            <!-- Informasi Aspirasi -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Aspirasi</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">ID Aspirasi</small>
                        <h6>#<?php echo $aspirasi['id_aspirasi']; ?></h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Judul</small>
                        <h4><?php echo $aspirasi['judul']; ?></h4>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Kategori</small><br>
                            <span class="badge bg-secondary"><?php echo $aspirasi['nama_kategori']; ?></span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Prioritas</small><br>
                            <?php
                            $prioritas_class = [
                                'rendah' => 'secondary',
                                'sedang' => 'warning',
                                'tinggi' => 'danger'
                            ];
                            ?>
                            <span class="badge bg-<?php echo $prioritas_class[$aspirasi['prioritas']]; ?>">
                                <?php echo strtoupper($aspirasi['prioritas']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Lokasi</small>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo $aspirasi['lokasi']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Tanggal Pengaduan</small>
                            <p><i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($aspirasi['tanggal_pengaduan'])); ?></p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted">Deskripsi Masalah</small>
                        <p class="mt-2"><?php echo nl2br($aspirasi['deskripsi']); ?></p>
                    </div>

                    <?php if ($aspirasi['foto']): ?>
                        <div class="mb-3">
                            <small class="text-muted">Foto Pendukung</small><br>
                            <img src="../assets/img/uploads/<?php echo $aspirasi['foto']; ?>" 
                                 class="img-fluid rounded mt-2" 
                                 style="max-height: 400px;"
                                 alt="Foto Aspirasi">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Umpan Balik -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i> Tanggapan Admin 
                        <span class="badge bg-white text-success"><?php echo count($umpan_balik); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($umpan_balik)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-hourglass-half fa-3x mb-3"></i>
                            <p>Belum ada tanggapan dari admin</p>
                            <small>Admin akan segera menindaklanjuti aspirasi Anda</small>
                        </div>
                    <?php else: ?>
                        <!-- Timeline Umpan Balik -->
                        <?php foreach ($umpan_balik as $index => $ub): ?>
                            <div class="card mb-3 border-start border-success border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>
                                            <i class="fas fa-user-shield text-success"></i> 
                                            <?php echo $ub['nama_admin']; ?>
                                        </strong>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> 
                                            <?php echo date('d/m/Y H:i', strtotime($ub['tanggal_umpan_balik'])); ?>
                                        </small>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <p class="mb-0"><?php echo nl2br($ub['isi_umpan_balik']); ?></p>
                                    </div>

                                    <?php if ($ub['progres_perbaikan']): ?>
                                        <div class="alert alert-info mb-2">
                                            <strong><i class="fas fa-tasks"></i> Progres Perbaikan:</strong>
                                            <p class="mb-0 mt-1"><?php echo nl2br($ub['progres_perbaikan']); ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($ub['estimasi_selesai']): ?>
                                        <div>
                                            <i class="fas fa-calendar-check text-warning"></i> 
                                            <strong>Estimasi Selesai:</strong> 
                                            <span class="badge bg-warning">
                                                <?php echo date('d F Y', strtotime($ub['estimasi_selesai'])); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Status Aspirasi</h6>
                </div>
                <div class="card-body text-center">
                    <?php
                    $status_info = [
                        'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Menunggu'],
                        'proses' => ['class' => 'info', 'icon' => 'spinner', 'text' => 'Sedang Diproses'],
                        'selesai' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Selesai'],
                        'ditolak' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Ditolak']
                    ];
                    $current_status = $status_info[$aspirasi['status']];
                    ?>
                    
                    <i class="fas fa-<?php echo $current_status['icon']; ?> fa-4x text-<?php echo $current_status['class']; ?> mb-3"></i>
                    <h4>
                        <span class="badge bg-<?php echo $current_status['class']; ?>">
                            <?php echo $current_status['text']; ?>
                        </span>
                    </h4>
                </div>
            </div>

            <!-- Info Tambahan -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-line"></i> Informasi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Jumlah Tanggapan</small>
                        <h5><?php echo count($umpan_balik); ?> Tanggapan</h5>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Terakhir Diupdate</small>
                        <p><?php echo date('d F Y H:i', strtotime($aspirasi['updated_at'])); ?></p>
                    </div>

                    <hr>

                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="fas fa-lightbulb"></i> 
                            <strong>Tips:</strong> Pantau halaman ini secara berkala untuk melihat perkembangan penanganan aspirasi Anda.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>