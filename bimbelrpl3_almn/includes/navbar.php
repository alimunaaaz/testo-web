<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">
            <i class="fas fa-school"></i> Pengaduan Sekolah
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    
                    <!-- Menu untuk ADMIN -->
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/index.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/aspirasi_list.php">
                                <i class="fas fa-list"></i> Daftar Aspirasi
                            </a>
                        </li>
                    
                    <!-- Menu untuk SISWA -->
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>siswa/index.php">
                                <i class="fas fa-home"></i> Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>siswa/form_aspirasi.php">
                                <i class="fas fa-plus-circle"></i> Buat Aspirasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>siswa/aspirasi_saya.php">
                                <i class="fas fa-clipboard-list"></i> Aspirasi Saya
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <div class="d-flex align-items-center">
                    <button onclick="toggleTheme()" class="btn btn-link text-white me-2 p-0 border-0 shadow-none" title="Ganti Tema">
                        <i class="fas fa-moon theme-icon"></i>
                    </button>

                    <li class="nav-item dropdown" style="list-style: none;">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1" style="font-size: 1.2rem;"></i> 
                            <span><?php echo $_SESSION['nama']; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li>
                                <a class="dropdown-item py-2" href="<?php echo $base_url . $_SESSION['role']; ?>/profile.php">
                                    <i class="fas fa-user-cog me-2 text-muted"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?php echo $base_url . $_SESSION['role']; ?>/security.php">
                                    <i class="fas fa-shield-alt me-2 text-muted"></i> Security
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="<?php echo $base_url; ?>logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </div>
                    
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>