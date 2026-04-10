<?php
/**
 * File: siswa/form_aspirasi.php
 * Fungsi: Form untuk membuat aspirasi baru
 */

require_once '../functions/functions.php';
check_siswa();

$base_url = '../';
$page_title = 'Buat Aspirasi Baru';

// Get semua kategori
$kategoris = get_all_kategori();

// Variable untuk pesan
$message = '';
$message_type = '';
$errors = [];

// Proses SUBMIT FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $judul = clean_input($_POST['judul']);
    $id_kategori = clean_input($_POST['id_kategori']);
    $lokasi = clean_input($_POST['lokasi']);
    $prioritas = clean_input($_POST['prioritas']);
    $tanggal = clean_input($_POST['tanggal_pengaduan']);
    $deskripsi = clean_input($_POST['deskripsi']);
    
    // Validasi wajib diisi
    if (empty($judul)) $errors[] = "Judul wajib diisi";
    if (empty($id_kategori)) $errors[] = "Kategori wajib dipilih";
    if (empty($lokasi)) $errors[] = "Lokasi wajib diisi";
    if (empty($prioritas)) $errors[] = "Prioritas wajib dipilih";
    if (empty($tanggal)) $errors[] = "Tanggal wajib diisi";
    if (empty($deskripsi)) $errors[] = "Deskripsi wajib diisi";
    
    // Upload foto (opsional)
    $foto_name = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $foto_name = upload_file($_FILES['foto']);
        
        if ($foto_name === false) {
            $errors[] = "Gagal upload foto. Pastikan format JPG/PNG/GIF dan ukuran max 5MB";
        }
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $data = [
            'id_user' => $_SESSION['user_id'],
            'id_kategori' => $id_kategori,
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'lokasi' => $lokasi,
            'prioritas' => $prioritas,
            'tanggal_pengaduan' => $tanggal,
            'foto' => $foto_name
        ];
        
        if (insert_aspirasi($data)) {
            // Berhasil - redirect ke halaman aspirasi saya
            header("Location: aspirasi_saya.php?success=1");
            exit();
        } else {
            $message = 'Gagal menyimpan aspirasi!';
            $message_type = 'danger';
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    .preview-image {
        max-width: 100%;
        max-height: 300px;
        margin-top: 10px;
        border-radius: 8px;
        display: none;
    }
</style>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="mb-4">
                <a href="index.php" class="btn btn-secondary mb-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h2><i class="fas fa-plus-circle"></i> Buat Aspirasi Baru</h2>
                <p class="text-muted">Sampaikan pengaduan Anda dengan lengkap dan jelas</p>
            </div>

            <!-- Alert Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong><i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Alert Message -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Formulir Aspirasi</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <!-- Judul -->
                        <div class="mb-3">
                            <label class="form-label">
                                Judul Pengaduan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="judul" 
                                   placeholder="Contoh: AC Ruang Kelas Rusak"
                                   value="<?php echo isset($_POST['judul']) ? $_POST['judul'] : ''; ?>"
                                   required>
                        </div>

                        <div class="row">
                            <!-- Kategori -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="id_kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($kategoris as $kat): ?>
                                        <option value="<?php echo $kat['id_kategori']; ?>"
                                            <?php echo (isset($_POST['id_kategori']) && $_POST['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
                                            <?php echo $kat['nama_kategori']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Lokasi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Lokasi <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="lokasi" 
                                       placeholder="Contoh: Ruang XII RPL 1"
                                       value="<?php echo isset($_POST['lokasi']) ? $_POST['lokasi'] : ''; ?>"
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Prioritas -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Prioritas <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="prioritas" required>
                                    <option value="">-- Pilih Prioritas --</option>
                                    <option value="rendah" <?php echo (isset($_POST['prioritas']) && $_POST['prioritas'] == 'rendah') ? 'selected' : ''; ?>>Rendah</option>
                                    <option value="sedang" <?php echo (isset($_POST['prioritas']) && $_POST['prioritas'] == 'sedang') ? 'selected' : ''; ?>>Sedang</option>
                                    <option value="tinggi" <?php echo (isset($_POST['prioritas']) && $_POST['prioritas'] == 'tinggi') ? 'selected' : ''; ?>>Tinggi</option>
                                </select>
                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Tanggal Kejadian <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" name="tanggal_pengaduan" 
                                       value="<?php echo isset($_POST['tanggal_pengaduan']) ? $_POST['tanggal_pengaduan'] : date('Y-m-d'); ?>"
                                       max="<?php echo date('Y-m-d'); ?>"
                                       required>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label class="form-label">
                                Deskripsi Masalah <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="deskripsi" rows="5" 
                                      placeholder="Jelaskan masalah secara detail..."
                                      required><?php echo isset($_POST['deskripsi']) ? $_POST['deskripsi'] : ''; ?></textarea>
                            <small class="text-muted">Jelaskan masalah dengan detail supaya mudah ditindaklanjuti</small>
                        </div>

                        <!-- Foto -->
                        <div class="mb-3">
                            <label class="form-label">
                                Foto Pendukung <span class="text-muted">(Opsional)</span>
                            </label>
                            <input type="file" class="form-control" name="foto" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif"
                                   onchange="previewImage(this)">
                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB</small>
                            
                            <!-- Preview Image -->
                            <img id="preview" class="preview-image" alt="Preview">
                        </div>

                        <hr>

                        <!-- Tombol -->
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Kirim Aspirasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image sebelum upload
function previewImage(input) {
    const preview = document.getElementById('preview');
    
    if (input.files && input.files[0]) {
        // Validasi ukuran file (5MB)
        if (input.files[0].size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB');
            input.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(input.files[0].type)) {
            alert('Format file tidak didukung! Gunakan JPG, PNG, atau GIF');
            input.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Tampilkan preview
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>