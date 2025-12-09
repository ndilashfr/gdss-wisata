<?php
// --- 0. CEK HAK AKSES (WAJIB DITARUH PALING ATAS) ---
// Kita harus definisikan dulu siapa yang login sebelum lanjut ke logika lain
$role_sekarang = strtolower($_SESSION['role'] ?? ''); 
$is_vip = in_array($role_sekarang, ['admin', 'kadispar']);

// --- 1. LOGIKA PHP: HITUNG & SORTING DI AWAL ---

// A. Cek Status Penilaian
$total_dm_required = 3; 
$qCek = mysqli_query($conn, "SELECT DISTINCT id_user FROM penilaian");
$dm_selesai = mysqli_num_rows($qCek);

// MODIFIKASI LOGIKA TAMPIL:
$is_ready = ($dm_selesai >= $total_dm_required); // Data sudah 3 orang (Siap)
$is_clicked = (isset($_GET['act']) && $_GET['act'] == 'run'); // Tombol diklik (URL trigger)

// LOGIKA PENENTUAN TAMPIL
if ($is_vip) {
    // KASUS 1: Jika KADISPAR/ADMIN (VIP)
    // Hasil hanya muncul jika sudah SIAP DAN tombol 'Jalankan' sudah DIKLIK
    $show_result = ($is_ready && $is_clicked); 
} else {
    // KASUS 2: Jika AKADEMISI/PHRI (Non-VIP)
    // Hasil LANGSUNG muncul otomatis begitu data siap (tanpa perlu klik tombol)
    $show_result = $is_ready; 
}

// B. Fungsi Helper TOPSIS (TIDAK DIUBAH)
function getTopsisRanking($conn, $role) {
    $qUser = mysqli_query($conn, "SELECT id_user FROM users WHERE role='$role'");
    $uData = mysqli_fetch_assoc($qUser);
    if(!$uData) return []; 
    $id_user = $uData['id_user'];

    $cekNilai = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_user='$id_user'");
    if(mysqli_num_rows($cekNilai) == 0) return [];

    $kriteria = []; $qK = mysqli_query($conn, "SELECT * FROM kriteria");
    while($r=mysqli_fetch_assoc($qK)) $kriteria[$r['id_kriteria']]=$r;
    
    $alternatif = []; $qA = mysqli_query($conn, "SELECT * FROM alternatif");
    while($r=mysqli_fetch_assoc($qA)) $alternatif[$r['id_alternatif']]=$r;

    $matriks_x=[]; $qN = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_user='$id_user'");
    while($r=mysqli_fetch_assoc($qN)) $matriks_x[$r['id_alternatif']][$r['id_kriteria']]=$r['nilai'];

    $bobot=[]; $qB = mysqli_query($conn, "SELECT * FROM bobot_user WHERE id_user='$id_user'");
    while($r=mysqli_fetch_assoc($qB)) $bobot[$r['id_kriteria']]=$r['nilai_bobot'];

    // Hitung TOPSIS
    $pembagi=[]; 
    foreach($kriteria as $idk=>$k){
        $sum=0; foreach($alternatif as $ida=>$a) $sum+=pow($matriks_x[$ida][$idk]??0, 2);
        $pembagi[$idk]=sqrt($sum);
    }
    $matriks_y=[];
    foreach($alternatif as $ida=>$a){
        foreach($kriteria as $idk=>$k){
            $r = ($pembagi[$idk]>0) ? ($matriks_x[$ida][$idk]??0)/$pembagi[$idk] : 0;
            $matriks_y[$ida][$idk] = $r * ($bobot[$idk]??0);
        }
    }
    $sol_plus=[]; $sol_min=[];
    foreach($kriteria as $idk=>$k){
        $col = array_column($matriks_y, $idk);
        if($k['atribut']=='benefit'){ $sol_plus[$idk]=max($col); $sol_min[$idk]=min($col); }
        else { $sol_plus[$idk]=min($col); $sol_min[$idk]=max($col); }
    }
    $hasil=[];
    foreach($alternatif as $ida=>$a){
        $dp=0; $dm=0;
        foreach($kriteria as $idk=>$k){
            $dp+=pow($matriks_y[$ida][$idk]-$sol_plus[$idk], 2);
            $dm+=pow($matriks_y[$ida][$idk]-$sol_min[$idk], 2);
        }
        $v = (sqrt($dm)+sqrt($dp)>0) ? sqrt($dm)/(sqrt($dm)+sqrt($dp)) : 0;
        $hasil[$ida] = $v;
    }
    arsort($hasil); 
    $ranked_list = []; $rank = 1;
    foreach($hasil as $id_alt => $score){ $ranked_list[$id_alt] = $rank++; }
    return $ranked_list;
}

// C. Ambil Data Ranking
$rank_dm1 = getTopsisRanking($conn, 'kadispar');
$rank_dm2 = getTopsisRanking($conn, 'phri');
$rank_dm3 = getTopsisRanking($conn, 'akademisi');

// D. PROSES DATA (Borda)
$data_alternatif = [];
$qAlt = mysqli_query($conn, "SELECT * FROM alternatif");
$jumlah_alternatif = mysqli_num_rows($qAlt);

while($row = mysqli_fetch_assoc($qAlt)) {
    $id = $row['id_alternatif'];
    
    // Hitung Poin
    $p1 = isset($rank_dm1[$id]) ? ($jumlah_alternatif - $rank_dm1[$id] + 1) : 0;
    $p2 = isset($rank_dm2[$id]) ? ($jumlah_alternatif - $rank_dm2[$id] + 1) : 0;
    $p3 = isset($rank_dm3[$id]) ? ($jumlah_alternatif - $rank_dm3[$id] + 1) : 0;
    $total = $p1 + $p2 + $p3;

    $row['poin_dm1'] = $p1;
    $row['poin_dm2'] = $p2;
    $row['poin_dm3'] = $p3;
    $row['total_poin'] = $total;
    
    $data_alternatif[$id] = $row;
}

// Sorting
uasort($data_alternatif, function($a, $b) {
    return $b['total_poin'] <=> $a['total_poin'];
});

// --- E. CEK HAK AKSES ---
$role_sekarang = strtolower($_SESSION['role'] ?? ''); 
$is_vip = in_array($role_sekarang, ['admin', 'kadispar']);
?>
<style>
    /* Style Khusus Header Ungu Soft */
    .header-soft-purple {
        background-color: #fdf4ff; /* Latar belakang ungu sangat muda */
        border-bottom: 2px solid #e9d5ff; /* Garis bawah ungu soft */
        color: #7e22ce; /* Teks Ungu Tua */
        padding: 20px 25px;
        font-weight: 500;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px;
        border-radius: 12px 12px 0 0;
    }

    .header-soft-purple i {
        font-size: 1.5rem;
        line-height: 1;
    }
    /* Header Gradient Ungu Pink */
    .hero-consensus {
        background: linear-gradient(90deg, #9333ea 0%, #db2777 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(147, 51, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .hero-consensus::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 20px 20px;
    }

    /* Alert Box Orange */
    .alert-waiting {
        background-color: #fff7ed;
        border: 1px solid #fdba74;
        color: #9a3412;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    /* Alert Box Ready (Biru) - BARU */
    .alert-ready {
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .icon-waiting {
        width: 40px; height: 40px;
        background: #f97316;
        color: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-right: 15px;
    }

    .table-compare thead th {
        background-color: #a855f7;
        color: white;
        border: none;
        padding: 15px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .circle-rank {
        width: 30px; height: 30px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        color: #6b7280;
        display: inline-flex;
        align-items: center; justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .circle-rank.top-1 { border-color: #f59e0b; color: #f59e0b; background: #fffbeb; }
    
    .table-borda thead th {
        background-color: #f3f4f6;
        color: #4b5563;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
    }
    .badge-rank-final {
        background: #f59e0b;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
    
    /* STYLE BARU: Kartu Kesimpulan */
    .conclusion-card {
        background: linear-gradient(135deg, #fffbeb 0%, #fff 100%);
        border: 2px solid #fbbf24;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }
    .conclusion-icon {
        background: #fbbf24;
        color: #78350f;
        width: 60px; height: 60px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 4px 10px rgba(251, 191, 36, 0.4);
    }
</style>

<div class="hero-consensus">
    <div style="position: relative; z-index: 2;">
        <h3 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i> Hasil Keputusan Kelompok</h3>
        <p class="mb-0 opacity-75">Konsensus menggunakan metode Borda Count dari hasil TOPSIS ketiga Decision Maker</p>
    </div>
</div>

<?php if(!$is_ready): ?>
    <div class="alert-waiting mb-4">
        <div class="d-flex align-items-center">
            <div class="icon-waiting"><i class="bi bi-exclamation-lg"></i></div>
            <div>
                <h6 class="fw-light mb-1">Menunggu penilaian dari semua DM</h6>
                <small>Konsensus baru dapat dihitung setelah 3 DM selesai.</small>
            </div>
        </div>
        
        <?php if($is_vip): ?>
            <button class="btn btn-secondary" disabled>Jalankan Konsensus</button>
        <?php endif; ?>
    </div>

<?php elseif($is_ready && !$show_result): ?>
    <div class="alert-ready mb-4">
        <div class="d-flex align-items-center">
            <div class="icon-waiting" style="background: #3b82f6;"><i class="bi bi-check-lg"></i></div>
            <div>
                <h6 class="fw-bold mb-1">Data Penilaian Lengkap!</h6>
                <small>Seluruh DM telah memberikan penilaian. Sistem siap melakukan perhitungan.</small>
            </div>
        </div>
        
        <?php if($is_vip): ?>
            <a href="index.php?page=konsensus&act=run" class="btn btn-primary fw-bold px-4 shadow-sm">
                <i class="bi bi-play-fill me-1"></i> Jalankan Konsensus
            </a>
        <?php else: ?>
             <button class="btn btn-secondary" disabled>Menunggu Eksekusi Admin/Kadispar</button>
        <?php endif; ?>
    </div>

<?php else: ?>
    <?php if($is_vip): ?>
        <div class="alert alert-success d-flex align-items-center mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
            <div>
                <strong>Perhitungan Selesai!</strong><br>
                Borda Count berhasil dijalankan. Hasil keputusan kelompok ada di bawah.
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info-custom d-flex align-items-center mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
            <div>
                <strong>Keputusan Final.</strong><br>
                Hasil konsensus akhir telah ditetapkan.
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>


<div class="card mb-4 border-0 shadow-sm">
    <div class="header-soft-purple">
        <i class="bi bi-people"></i> <span>Perbandingan Ranking Individual</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-compare mb-0 text-center align-middle">
                <thead>
                    <tr>
                        <th class="text-start ps-4">Alternatif</th>
                        <th>Rank DM 1<br><small class="opacity-75">(Kadispar)</small></th>
                        <th>Rank DM 2<br><small class="opacity-75">(PHRI)</small></th>
                        <th>Rank DM 3<br><small class="opacity-75">(Akademisi)</small></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($data_alternatif as $id => $alt): 
                        $r1 = $rank_dm1[$id] ?? '-';
                        $r2 = $rank_dm2[$id] ?? '-';
                        $r3 = $rank_dm3[$id] ?? '-';
                    ?>
                    <tr>
                        <td class="text-start ps-4 fw-light"><?= $alt['nama_wisata'] ?></td>
                        <td><div class="circle-rank <?= ($r1==1)?'top-1':''; ?>"><?= $r1 ?></div></td>
                        <td><div class="circle-rank <?= ($r2==1)?'top-1':''; ?>"><?= $r2 ?></div></td>
                        <td><div class="circle-rank <?= ($r3==1)?'top-1':''; ?>"><?= $r3 ?></div></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if($show_result): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold text-dark mb-0">Hasil Keputusan Kelompok (Borda Count)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-borda mb-0 text-center align-middle">
                <thead>
                    <tr>
                        <th class="text-start ps-4">Alternatif</th>
                        <th>Poin DM 1</th>
                        <th>Poin DM 2</th>
                        <th>Poin DM 3</th>
                        <th class="bg-light">Total Poin</th>
                        <th>Ranking Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $final_rank = 1;
                    foreach($data_alternatif as $res): 
                    ?>
                    <tr>
                        <td class="text-start ps-4 fw-light text-dark"><?= $res['nama_wisata'] ?></td>
                        <td class="text-muted small"><?= $res['poin_dm1'] ?> Poin</td>
                        <td class="text-muted small"><?= $res['poin_dm2'] ?> Poin</td>
                        <td class="text-muted small"><?= $res['poin_dm3'] ?> Poin</td>
                        <td class="fw-light fs-5 text-primary bg-light"><?= $res['total_poin'] ?></td>
                        <td>
                            <?php if($final_rank == 1): ?>
                                <span class="badge badge-rank-final">Rank 1 🏆</span>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-pill">Rank <?= $final_rank ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $final_rank++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Logika menentukan Alasan
// 1. Ambil Juara 1
$juara = reset($data_alternatif); // Ambil elemen pertama (sudah di-sort)
$id_juara = $juara['id_alternatif'];

// 2. Cek apakah dia Rank 1 di masing-masing DM?
$count_rank1 = 0;
if(($rank_dm1[$id_juara]??0) == 1) $count_rank1++;
if(($rank_dm2[$id_juara]??0) == 1) $count_rank1++;
if(($rank_dm3[$id_juara]??0) == 1) $count_rank1++;

// 3. Buat Kalimat Alasan
$alasan = "";
if($count_rank1 == 3) {
    $alasan = "Destinasi ini menjadi <strong>pilihan mutlak (Peringkat 1)</strong> dari seluruh pengambil keputusan (Dinas Pariwisata, PHRI, dan Akademisi), sehingga menjadi rekomendasi yang paling kuat dan tidak terbantahkan.";
} elseif($count_rank1 >= 1) {
    $alasan = "Destinasi ini berhasil mengumpulkan total poin tertinggi karena mendapatkan peringkat teratas dari sebagian besar pengambil keputusan dan memiliki performa yang stabil di penilaian lainnya.";
} else {
    $alasan = "Meskipun tidak selalu menjadi juara 1 di penilaian individu, destinasi ini memiliki <strong>konsistensi nilai tertinggi</strong> secara rata-rata (konsensus) dibandingkan alternatif lain, menjadikannya jalan tengah terbaik.";
}
?>

<div class="conclusion-card p-4">
    <div class="d-flex align-items-start gap-4">
        <div class="conclusion-icon flex-shrink-0">
            <i class="bi bi-trophy-fill"></i>
        </div>
        <div>
            <h5 class="fw-bold text-dark mb-2">Rekomendasi Terbaik: <span class="text-primary"><?= $juara['nama_wisata'] ?></span></h5>
            <p class="text-muted mb-2">
                Berdasarkan hasil perhitungan metode Borda Count dengan total perolehan <strong><?= $juara['total_poin'] ?> Poin</strong>.
            </p>
            <div class="p-3 bg-white rounded border border-warning-subtle">
                <strong class="d-block mb-1 text-warning-emphasis"><i class="bi bi-lightbulb me-1"></i> Alasan Rekomendasi:</strong>
                <span class="text-secondary small" style="line-height: 1.6;"><?= $alasan ?></span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>