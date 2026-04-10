<?php
/**
 * File: admin/detail_aspirasi.php
 * Fungsi: Halaman detail aspirasi + update status + tambah umpan balik
 */

require_once '../functions/functions.php';
check_admin();

$base_url = '../';
$page_title = 'Detail Aspirasi - Admin';

// Cek apakah ada ID aspirasi
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: aspirasi_list.php");
    exit();
}

$id_aspirasi = $_GET['id'];

// Ambil data aspirasi
$aspirasi = get_aspirasi_by_id($id_aspirasi);

if (!$aspirasi) {
    header("Location: aspirasi_list.php");
    exit();
}

// Ambil umpan balik
$umpan_balik = get_umpan_balik($id_aspirasi);

// Variable untuk pesan
$message = '';
$message_type = '';

// Proses UPDATE STATUS
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = clean_input($_POST['status']);
    
    if (update_status_aspirasi($id_aspirasi, $new_status)) {
        $message = 'Status berhasil diupdate!';
        $message_type = 'success';
        
        // Refresh data
        $aspirasi = get_aspirasi_by_id($id_aspirasi);
    } else {
        $message = 'Gagal update status!';
        $message_type = 'danger';
    }
}

// Proses TAMBAH UMPAN BALIK
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_umpan_balik'])) {
    $data = [
        'id_aspirasi' => $id_aspirasi,
        'id_admin' => $_SESSION['user_id'],
        'isi_umpan_balik' => clean_input($_POST['isi_umpan_balik']),
        'progres_perbaikan' => clean_input($_POST['progres_perbaikan']),
        'estimasi_selesai' => !empty($_POST['estimasi_selesai']) ? $_POST['estimasi_selesai'] : null
    ];
    
    if (insert_umpan_balik($data)) {
        $message = 'Umpan balik berhasil ditambahkan!';
        $message_type = 'success';
        
        // Refresh data
        $umpan_balik = get_umpan_balik($id_aspirasi);
    } else {
        $message = 'Gagal menambahkan umpan balik!';
        $message_type = 'danger';
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <a href="aspirasi_list.php" class="btn btn-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h2><i class="fas fa-file-alt"></i> Detail Aspirasi #<?php echo $aspirasi['id_aspirasi']; ?></h2>
        </div>
    </div>

    <!-- Alert Message -->
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Kolom Kiri: Detail Aspirasi -->
        <div class="col-md-8">
            <!-- Informasi Utama -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Aspirasi</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>ID Aspirasi:</strong><br>
                            <span class="badge bg-secondary">#<?php echo $aspirasi['id_aspirasi']; ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Tanggal Pengaduan:</strong><br>
                            <?php echo date('d F Y', strtotime($aspirasi['tanggal_pengaduan'])); ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Nama Siswa:</strong><br>
                            <?php echo $aspirasi['nama_siswa']; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Kelas:</strong><br>
                            <?php echo $aspirasi['kelas']; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Judul:</strong>
                        <h4><?php echo $aspirasi['judul']; ?></h4>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Kategori:</strong><br>
                            <span class="badge bg-secondary"><?php echo $aspirasi['nama_kategori']; ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Lokasi:</strong><br>
                            <i class="fas fa-map-marker-alt"></i> <?php echo $aspirasi['lokasi']; ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Prioritas:</strong><br>
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

                    <div class="mb-3">
                        <strong>Status Saat Ini:</strong><br>
                        <?php
                        $status_class = [
                            'pending' => 'warning',
                            'proses' => 'info',
                            'selesai' => 'success',
                            'ditolak' => 'danger'
                        ];
                        ?>
                        <span class="badge bg-<?php echo $status_class[$aspirasi['status']]; ?> fs-6">
                            <?php echo strtoupper($aspirasi['status']); ?>
                        </span>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Deskripsi:</strong>
                        <p class="mt-2"><?php echo nl2br($aspirasi['deskripsi']); ?></p>
                    </div>

                    <?php if ($aspirasi['foto']): ?>
                        <div class="mb-3">
                            <strong>Foto Pendukung:</strong><br>
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
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i> Riwayat Umpan Balik 
                        <span class="badge bg-white text-info"><?php echo count($umpan_balik); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($umpan_balik)): ?>
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada umpan balik</p>
                        </div>
                    <?php else: ?>
                        <!-- Timeline Umpan Balik -->
                        <div class="timeline">
                            <?php foreach ($umpan_balik as $ub): ?>
                                <div class="card mb-3 border-start border-primary border-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <strong>
                                                <i class="fas fa-user-shield text-primary"></i> 
                                                <?php echo $ub['nama_admin']; ?>
                                            </strong>
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> 
                                                <?php echo date('d/m/Y H:i', strtotime($ub['tanggal_umpan_balik'])); ?>
                                            </small>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <strong>Tanggapan:</strong>
                                            <p class="mb-0 mt-1"><?php echo nl2br($ub['isi_umpan_balik']); ?></p>
                                        </div>

                                        <?php if ($ub['progres_perbaikan']): ?>
                                            <div class="mb-2">
                                                <strong>Progres Perbaikan:</strong>
                                                <p class="mb-0 mt-1"><?php echo nl2br($ub['progres_perbaikan']); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($ub['estimasi_selesai']): ?>
                                            <div>
                                                <strong>Estimasi Selesai:</strong> 
                                                <span class="badge bg-warning">
                                                    <?php echo date('d F Y', strtotime($ub['estimasi_selesai'])); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Aksi -->
        <div class="col-md-4">
            <!-- Form Update Status -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h6 class="mb-0"><i class="fas fa-edit"></i> Update Status</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Status Baru</label>
                            <select class="form-select" name="status" required>
                                <option value="pending" <?php echo $aspirasi['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="proses" <?php echo $aspirasi['status'] == 'proses' ? 'selected' : ''; ?>>Dalam Proses</option>
                                <option value="selesai" <?php echo $aspirasi['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                <option value="ditolak" <?php echo $aspirasi['status'] == 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-warning w-100">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Tambah Umpan Balik -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-plus"></i> Tambah Umpan Balik</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Isi Tanggapan *</label>
                            <textarea class="form-control" name="isi_umpan_balik" 
                                      rows="3" required 
                                      placeholder="Tulis tanggapan Anda..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Progres Perbaikan</label>
                            <textarea class="form-control" name="progres_perbaikan" 
                                      rows="2" 
                                      placeholder="Contoh: Teknisi sudah dipanggil..."></textarea>
                            <small class="text-muted">Opsional</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estimasi Selesai</label>
                            <input type="date" class="form-control" name="estimasi_selesai">
                            <small class="text-muted">Opsional</small>
                        </div>

                        <button type="submit" name="tambah_umpan_balik" class="btn btn-success w-100">
                            <i class="fas fa-paper-plane"></i> Kirim Umpan Balik
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>