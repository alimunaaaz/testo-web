<?php
/**
 * File: admin/aspirasi_list.php
 * Fungsi: Daftar semua aspirasi dengan filter
 */

require_once '../functions/functions.php';
check_admin();

$base_url = '../';
$page_title = 'Daftar Aspirasi - Admin';

// Ambil data untuk dropdown filter
$conn = getConnection();

// Get semua users (siswa)
$users_result = $conn->query("SELECT id_user, nama, kelas FROM users WHERE role = 'siswa' ORDER BY nama");
$users = [];
while ($row = $users_result->fetch_assoc()) {
    $users[] = $row;
}

// Get semua kategori
$kategori_result = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori");
$kategoris = [];
while ($row = $kategori_result->fetch_assoc()) {
    $kategoris[] = $row;
}

closeConnection($conn);

// Ambil filter dari GET
$filters = [];

if (isset($_GET['tanggal']) && !empty($_GET['tanggal'])) {
    $filters['tanggal'] = $_GET['tanggal'];
}

if (isset($_GET['bulan']) && !empty($_GET['bulan'])) {
    // Format bulan: 2025-02
    $filters['bulan'] = $_GET['bulan'];
}

if (isset($_GET['id_user']) && !empty($_GET['id_user'])) {
    $filters['id_user'] = $_GET['id_user'];
}

if (isset($_GET['id_kategori']) && !empty($_GET['id_kategori'])) {
    $filters['id_kategori'] = $_GET['id_kategori'];
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}

// Get aspirasi dengan filter
$aspirasi_list = get_all_aspirasi($filters);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-list"></i> Daftar Aspirasi</h2>
            <p class="text-muted">Kelola semua pengaduan siswa</p>
        </div>
    </div>

    <!-- Form Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Aspirasi</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="">
                <div class="row g-3">
                    <!-- Filter Tanggal -->
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" 
                               value="<?php echo isset($_GET['tanggal']) ? $_GET['tanggal'] : ''; ?>">
                    </div>

                    <!-- Filter Bulan -->
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <input type="month" class="form-control" name="bulan" 
                               value="<?php echo isset($_GET['bulan']) ? $_GET['bulan'] : ''; ?>">
                    </div>

                    <!-- Filter Siswa -->
                    <div class="col-md-3">
                        <label class="form-label">Siswa</label>
                        <select class="form-select" name="id_user">
                            <option value="">-- Semua Siswa --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id_user']; ?>" 
                                    <?php echo (isset($_GET['id_user']) && $_GET['id_user'] == $user['id_user']) ? 'selected' : ''; ?>>
                                    <?php echo $user['nama'] . ' (' . $user['kelas'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Kategori -->
                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="id_kategori">
                            <option value="">-- Semua Kategori --</option>
                            <?php foreach ($kategoris as $kat): ?>
                                <option value="<?php echo $kat['id_kategori']; ?>" 
                                    <?php echo (isset($_GET['id_kategori']) && $_GET['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
                                    <?php echo $kat['nama_kategori']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="proses" <?php echo (isset($_GET['status']) && $_GET['status'] == 'proses') ? 'selected' : ''; ?>>Dalam Proses</option>
                            <option value="selesai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                            <option value="ditolak" <?php echo (isset($_GET['status']) && $_GET['status'] == 'ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="col-md-9">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="aspirasi_list.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Aspirasi -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Hasil: <?php echo count($aspirasi_list); ?> Aspirasi</h5>
        </div>
        <div class="card-body">
            <?php if (empty($aspirasi_list)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data aspirasi sesuai filter</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aspirasi_list as $index => $asp): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($asp['tanggal_pengaduan'])); ?></td>
                                <td>
                                    <strong><?php echo $asp['nama_siswa']; ?></strong><br>
                                    <small class="text-muted"><?php echo $asp['kelas']; ?></small>
                                </td>
                                <td><?php echo $asp['judul']; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $asp['nama_kategori']; ?></span></td>
                                <td><?php echo $asp['lokasi']; ?></td>
                                <td>
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
                                </td>
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