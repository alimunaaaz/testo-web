<?php
include '../includes/header.php';
include '../includes/navbar.php';

// Proteksi halaman (Hanya Admin)
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm text-center p-4 mb-4">
                <div class="profile-img mb-3">
                    <i class="fas fa-user-circle fa-7x text-primary"></i>
                </div>
                <h4><?php echo $_SESSION['nama']; ?></h4>
                <p class="text-muted text-uppercase small"><?php echo $_SESSION['role']; ?></p>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm p-4">
                <h5 class="mb-4"><i class="fas fa-id-card me-2"></i> Detail Profil</h5>
                <form action="process_profile.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" class="form-control" value="<?php echo $_SESSION['username']; ?>" readonly>
                        <div class="form-text text-danger">*Username tidak dapat diubah.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo $_SESSION['nama']; ?>" required>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary px-4">Update Profil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>