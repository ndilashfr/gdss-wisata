<?php
// --- 1. LOGIKA PHP: HITUNG BORDA OTOMATIS ---

// A. Cek Status Penilaian Semua DM
$total_dm_required = 3; // Kadispar, PHRI, Akademisi
$qCek = mysqli_query($conn, "SELECT DISTINCT id_user FROM penilaian");
$dm_selesai = mysqli_num_rows($qCek);
$is_completed = ($dm_selesai >= $total_dm_required);

// B. Fungsi Helper: Hitung Ranking TOPSIS per Role (Tanpa merubah file lain)
function getTopsisRanking($conn, $role) {
    // 1. Ambil User ID berdasarkan Role
    $qUser = mysqli_query($conn, "SELECT id_user FROM users WHERE role='$role'");
    $uData = mysqli_fetch_assoc($qUser);
    if(!$uData) return []; // User tidak ditemukan
    $id_user = $uData['id_user'];

    // 2. Cek apakah user ini sudah menilai
    $cekNilai = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_user='$id_user'");
    if(mysqli_num_rows($cekNilai) == 0) return [];

    // 3. Hitung TOPSIS (Versi Ringkas)
    // Ambil data matriks & bobot
    $kriteria = []; $qK = mysqli_query($conn, "SELECT * FROM kriteria");
    while($r=mysqli_fetch_assoc($qK)) $kriteria[$r['id_kriteria']]=$r;
    
    $alternatif = []; $qA = mysqli_query($conn, "SELECT * FROM alternatif");
    while($r=mysqli_fetch_assoc($qA)) $alternatif[$r['id_alternatif']]=$r;

    $matriks_x=[]; $qN = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_user='$id_user'");
    while($r=mysqli_fetch_assoc($qN)) $matriks_x[$r['id_alternatif']][$r['id_kriteria']]=$r['nilai'];

    $bobot=[]; $qB = mysqli_query($conn, "SELECT * FROM bobot_user WHERE id_user='$id_user'");
    while($r=mysqli_fetch_assoc($qB)) $bobot[$r['id_kriteria']]=$r['nilai_bobot'];

    // Hitung V
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
    
    // Sort High to Low (Ranking)
    arsort($hasil); 
    
    // Convert Score to Rank Number (1, 2, 3...)
    $ranked_list = [];
    $rank = 1;
    foreach($hasil as $id_alt => $score){
        $ranked_list[$id_alt] = $rank++; // Simpan ID Alternatif => Rankingnya
    }
    
    return $ranked_list;
}

// C. Ambil Data Ranking dari 3 DM
$rank_dm1 = getTopsisRanking($conn, 'kadispar');
$rank_dm2 = getTopsisRanking($conn, 'phri');
$rank_dm3 = getTopsisRanking($conn, 'akademisi');

// Ambil Data Alternatif untuk Display
$data_alternatif = [];
$qAlt = mysqli_query($conn, "SELECT * FROM alternatif");
while($row = mysqli_fetch_assoc($qAlt)) { $data_alternatif[$row['id_alternatif']] = $row; }
$jumlah_alternatif = count($data_alternatif);

?>

<style>
    /* Style Khusus Header Ungu Soft */
    .header-soft-purple {
        background-color: #fdf4ff; /* Latar belakang ungu sangat muda (hampir putih) */
        border-bottom: 2px solid #e9d5ff; /* Garis bawah ungu soft */
        color: #7e22ce; /* Teks Ungu Tua */
        padding: 20px 25px;
        font-weight: 500; /* Font tidak terlalu tebal */
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px; /* Jarak antara ikon dan teks */
        border-radius: 12px 12px 0 0; /* Lengkungan sudut atas */
    }

    /* Ukuran Icon */
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
    
    /* Background Pattern dots (hiasan) */
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
    .icon-waiting {
        width: 40px; height: 40px;
        background: #f97316;
        color: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-right: 15px;
    }

    /* Table Styling Comparison */
    .table-compare thead th {
        background-color: #a855f7; /* Ungu Header */
        color: white;
        border: none;
        padding: 15px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    /* Lingkaran Ranking (1, 2, 3) */
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
    
    /* Tabel Hasil Akhir (Borda) */
    .table-borda thead th {
        background-color: #f3f4f6; /* Abu-abu */
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
</style>

<div class="hero-consensus">
    <div style="position: relative; z-index: 2;">
        <h3 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i> Hasil Keputusan Kelompok</h3>
        <p class="mb-0 opacity-75">Konsensus menggunakan metode Borda Count dari hasil TOPSIS ketiga Decision Maker</p>
    </div>
</div>

<?php if(!$is_completed): ?>
    <div class="alert-waiting mb-4">
        <div class="d-flex align-items-center">
            <div class="icon-waiting"><i class="bi bi-exclamation-lg"></i></div>
            <div>
                <h6 class="fw-light mb-1">Menunggu penilaian dari semua DM</h6>
                <small>Konsensus baru dapat dihitung setelah 3 DM (Kadispar, PHRI, Akademisi) menyelesaikan penilaian.</small>
            </div>
        </div>
        <button class="btn btn-secondary" disabled>Jalankan Konsensus</button>
    </div>
<?php else: ?>
    <div class="alert alert-success d-flex align-items-center mb-4 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
        <div>
            <strong>Semua DM telah menilai!</strong><br>
            Berikut adalah hasil perhitungan metode Borda Count.
        </div>
    </div>
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
                    <?php foreach($data_alternatif as $id => $alt): 
                        $r1 = $rank_dm1[$id] ?? '-';
                        $r2 = $rank_dm2[$id] ?? '-';
                        $r3 = $rank_dm3[$id] ?? '-';
                    ?>
                    <tr>
                        <td class="text-start ps-4 fw-light"><?= $alt['nama_wisata'] ?></td>
                        <td>
                            <div class="circle-rank <?= ($r1==1)?'top-1':''; ?>"><?= $r1 ?></div>
                        </td>
                        <td>
                            <div class="circle-rank <?= ($r2==1)?'top-1':''; ?>"><?= $r2 ?></div>
                        </td>
                        <td>
                            <div class="circle-rank <?= ($r3==1)?'top-1':''; ?>"><?= $r3 ?></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if($is_completed): ?>
<div class="card border-0 shadow-sm">
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
                    // HITUNG POIN BORDA
                    // Rumus: Poin = (Jumlah Alternatif - Ranking + 1)
                    // Misal 5 Alt: Rank 1 = 5 Poin, Rank 5 = 1 Poin.
                    
                    $borda_scores = [];
                    foreach($data_alternatif as $id => $alt){
                        $p1 = isset($rank_dm1[$id]) ? ($jumlah_alternatif - $rank_dm1[$id] + 1) : 0;
                        $p2 = isset($rank_dm2[$id]) ? ($jumlah_alternatif - $rank_dm2[$id] + 1) : 0;
                        $p3 = isset($rank_dm3[$id]) ? ($jumlah_alternatif - $rank_dm3[$id] + 1) : 0;
                        $total = $p1 + $p2 + $p3;
                        
                        $borda_scores[] = [
                            'nama' => $alt['nama_wisata'],
                            'p1' => $p1, 'p2' => $p2, 'p3' => $p3,
                            'total' => $total
                        ];
                    }

                    // Sorting Ranking Borda (Total Poin Tertinggi di Atas)
                    usort($borda_scores, function($a, $b) {
                        return $b['total'] <=> $a['total'];
                    });

                    // Tampilkan Tabel
                    $final_rank = 1;
                    foreach($borda_scores as $res):
                    ?>
                    <tr>
                        <td class="text-start ps-4 fw-light text-dark"><?= $res['nama'] ?></td>
                        <td class="text-muted small"><?= $res['p1'] ?> Poin</td>
                        <td class="text-muted small"><?= $res['p2'] ?> Poin</td>
                        <td class="text-muted small"><?= $res['p3'] ?> Poin</td>
                        <td class="fw-light fs-5 text-primary bg-light"><?= $res['total'] ?></td>
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
<?php endif; ?>