<div class="d-flex flex-column h-100">
    
    <div class="d-flex align-items-center gap-3 mb-4 px-2">
        <div class="logo-wrapper">
            <img src="assets/image/logo.png" alt="Logo" class="custom-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <i class="bi bi-stars text-white fs-4" style="display:none;"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-light text-white" style="letter-spacing: 0.5px;">GDSS Wisata</h6>
            <small class="text-white-50" style="font-size: 0.7rem;">Kota Batu 2025</small>
        </div>
    </div>

    <div class="user-profile-box px-2 d-flex align-items-center gap-3">
        <div class="avatar-circle">
            <?php echo isset($_SESSION['role']) ? strtoupper(substr($_SESSION['role'], 0, 1)) : 'U'; ?>
        </div>
        <div style="line-height: 1.2;">
            <small class="text-white-50" style="font-size: 0.7rem;">Logged in as</small>
            <div class="fw-light text-white mb-0" style="font-size: 0.9rem;">
                <?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User'; ?>
            </div>
            <small class="text-info fw-light" style="font-size: 0.75rem;">
                <?php echo isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Guest'; ?>
            </small>
        </div>
    </div>

    <ul class="nav flex-column flex-grow-1">
        
        <li class="nav-item">
            <a class="nav-link-custom <?php echo (!isset($_GET['page']) || $_GET['page']=='dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link-custom <?php echo (isset($_GET['page']) && $_GET['page']=='master_data') ? 'active' : ''; ?>" href="index.php?page=master_data">
                <i class="bi bi-database"></i>
                <span>Data Master</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link-custom <?php echo (isset($_GET['page']) && $_GET['page']=='pengaturan_bobot') ? 'active' : ''; ?>" href="index.php?page=pengaturan_bobot">
                <i class="bi bi-sliders"></i>
                <span>Pengaturan Bobot</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link-custom <?php echo (isset($_GET['page']) && $_GET['page']=='penilaian') ? 'active' : ''; ?>" href="index.php?page=penilaian">
                <i class="bi bi-clipboard-check"></i>
                <span>Penilaian Saya</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link-custom <?php echo (isset($_GET['page']) && $_GET['page']=='hasil_individu') ? 'active' : ''; ?>" href="index.php?page=hasil_individu">
                <i class="bi bi-bar-chart-line"></i>
                <span>Hasil Saya</span>
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-item mt-2">
            <a class="nav-link-custom nav-link-special <?php echo (isset($_GET['page']) && $_GET['page']=='konsensus') ? 'active' : ''; ?>" href="index.php?page=konsensus">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-people"></i>
                        <span>Hasil Konsensus</span>
                    </div>
                    <i class="bi bi-trophy-fill" style="font-size: 0.8rem;"></i>
                </div>
            </a>
        </li>

    </ul>
    
    <div class="mt-auto border-top border-secondary pt-3 px-2">
        <a class="nav-link-custom text-white-50 hover-danger" href="login.php">
            <i class="bi bi-box-arrow-left"></i>
            <span>Logout</span>
        </a>
    </div>

</div>