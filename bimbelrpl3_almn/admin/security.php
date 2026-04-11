<?php
include '../includes/header.php';
include '../includes/navbar.php';

// Proteksi halaman
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm p-4">
                <h5 class="mb-4 text-danger"><i class="fas fa-shield-alt me-2"></i> Keamanan Akun</h5>
                <p class="text-muted small">Disarankan untuk mengganti password secara berkala untuk menjaga keamanan data aspirasi.</p>
                
                <form action="process_security.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Saat Ini</label>
                        <input type="password" name="old_password" class="form-control" placeholder="Masukkan password lama" required>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru" required>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-danger">Ganti Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>