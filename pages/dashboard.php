<?php
// Pastikan $conn tersedia. Jika halaman ini di-include dari index.php, $conn otomatis ada.
// Jika diakses langsung, kita cegah error:
if(!isset($conn)) {
    // Opsional: include '../config/database.php'; tapi sebaiknya akses via index.php
    die("Akses langsung dilarang."); 
}

// Hitung data untuk statistik
$t_alt = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alternatif"));
$t_krit = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM kriteria"));
$t_user_rated = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT id_user FROM penilaian"));
$total_dm = 3; 
?>

<style>
    /* Styling Kartu Atas */
    .card-metric {
        border: none;
        border-radius: 20px;
        color: white;
        padding: 25px;
        height: 160px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .card-metric:hover { transform: translateY(-5px); }

    .bg-icon {
        position: absolute; right: 15px; top: 50%;
        transform: translateY(-50%); font-size: 5rem; opacity: 0.15;
    }

    .metric-value { font-size: 3rem; font-weight: 200; line-height: 1; margin-bottom: 5px; }
    .metric-label { font-size: 0.9rem; font-weight: 200; opacity: 0.9; }

    /* Gradasi Kartu Atas */
    .bg-gradient-purple { background: linear-gradient(135deg, #d946ef 0%, #a855f7 100%); }
    .bg-gradient-cyan { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-pink { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); }
    .bg-gradient-blue { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }

    /* --- INI SYNTAX UNTUK ICON BULAT GRADASI (YANG KAMU TANYAKAN) --- */
    .icon-circle-gradient {
        width: 48px;
        height: 48px;
        border-radius: 50%; /* Membuat jadi bulat */
        /* Gradasi Ungu ke Biru seperti di gambar */
        background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 4px 10px rgba(168, 85, 247, 0.3); /* Efek bayangan ungu */
    }

    .status-card {
        background: white; border-radius: 20px; padding: 30px;
        border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.02);
    }
    
    .dm-status-box {
        border: 1px solid #f0f0f0; border-radius: 15px; padding: 20px;
        display: flex; align-items: center; gap: 15px; background: #fff;
    }
    
    .dm-avatar {
        width: 50px; height: 50px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; color: white; font-size: 1.1rem;
    }
    
    .status-badge {
        font-size: 0.75rem; padding: 5px 12px; border-radius: 15px;
    }
</style>
<h2 class="mt-4 fw-light" style="color: #6d28d9;">Dashboard</h2>
                    <p class="text-muted">Ringkasan status penilaian dan hasil konsensus</p>
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card-metric bg-gradient-purple">
            <div class="d-flex flex-column justify-content-end h-100">
                <div class="metric-value"><?= $t_alt ?></div>
                <div class="metric-label">Destinasi Wisata</div>
            </div>
                        <i class="bi bi-geo-alt-fill bg-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-metric bg-gradient-cyan">
            <div class="d-flex flex-column justify-content-end h-100">
                <div class="metric-value"><?= $t_krit ?></div>
                <div class="metric-label">Parameter Penilaian</div>
            </div>
            <i class="bi bi-list-task bg-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-metric bg-gradient-pink">
            <div class="d-flex flex-column justify-content-end h-100">
                <div class="metric-value"><?= $t_user_rated ?>/<?= $total_dm ?></div>
                <div class="metric-label">DM Selesai Menilai</div>
            </div>
            <i class="bi bi-people-fill bg-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-metric bg-gradient-blue">
            <div class="d-flex flex-column justify-content-end h-100">
                <div class="metric-value">
                    <?php if($t_user_rated == $total_dm): ?>
                        <i class="bi bi-check-lg"></i>
                    <?php else: ?>
                        <div style="height: 3px; width: 40px; background: rgba(255,255,255,0.5); margin-bottom: 20px;"></div>
                    <?php endif; ?>
                </div>
                <div class="metric-label"><?= ($t_user_rated == $total_dm) ? 'Selesai' : 'Belum Selesai'; ?></div>
            </div>
            <i class="bi bi-trophy-fill bg-icon"></i>
        </div>
    </div>
</div>

<div class="status-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="icon-circle-gradient">
            <i class="bi bi-people"></i>
        </div>
        <div>
            <h5 class="fw-light mb-0 text-dark">Status Penilaian</h5>
            <small class="text-muted">Pantau progres pengisian nilai decision maker</small>
        </div>
    </div>

    <div class="row g-3">
        <?php
        $dms = [
            ['role' => 'kadispar', 'label' => 'Kepala Dinas Pariwisata', 'color' => '#a855f7'],
            ['role' => 'phri', 'label' => 'Perwakilan PHRI', 'color' => '#06b6d4'],
            ['role' => 'akademisi', 'label' => 'Akademisi Pariwisata', 'color' => '#f43f5e']
        ];

        foreach($dms as $idx => $dm){
            $qUser = mysqli_query($conn, "SELECT id_user FROM users WHERE role='{$dm['role']}'");
            $uData = mysqli_fetch_assoc($qUser);
            $isRated = false;
            
            if($uData){
                $cek = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_user='{$uData['id_user']}'");
                if(mysqli_num_rows($cek) > 0) $isRated = true;
            }
        ?>
        <div class="col-md-4">
            <div class="dm-status-box shadow-sm">
                <div class="dm-avatar" style="background-color: <?= $dm['color'] ?>;">
                    DM<?= $idx+1 ?>
                </div>
                <div>
                    <div class="fw-light text-dark" style="font-size: 0.9rem;"><?= $dm['label'] ?></div>
                    <div class="mt-2">
                        <?php if($isRated): ?>
                            <span class="status-badge bg-success text-white"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                        <?php else: ?>
                            <span class="status-badge bg-light text-secondary">Belum Menilai</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>