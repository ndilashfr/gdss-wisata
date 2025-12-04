<?php
// --- 1. LOGIKA PHP: PERHITUNGAN TOPSIS ---
$id_user = $_SESSION['user_id'];

// Cek apakah user sudah menilai
$cek = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_user = '$id_user'");
if(mysqli_num_rows($cek) == 0){
    echo "<div class='alert alert-warning py-5 text-center'>
            <i class='bi bi-exclamation-circle fs-1 d-block mb-3'></i>
            <h5>Anda belum melakukan penilaian.</h5>
            <p>Silakan input penilaian terlebih dahulu pada menu Penilaian Saya.</p>
            <a href='index.php?page=penilaian' class='btn btn-primary mt-2'>Input Penilaian</a>
          </div>";
    return; // Stop script
}

// A. AMBIL DATA
$kriteria = [];
$qKrit = mysqli_query($conn, "SELECT * FROM kriteria");
while($row = mysqli_fetch_assoc($qKrit)) { $kriteria[$row['id_kriteria']] = $row; }

$alternatif = [];
$qAlt = mysqli_query($conn, "SELECT * FROM alternatif");
while($row = mysqli_fetch_assoc($qAlt)) { $alternatif[$row['id_alternatif']] = $row; }

// Ambil Bobot User
$bobot = [];
$qBobot = mysqli_query($conn, "SELECT * FROM bobot_user WHERE id_user = '$id_user'");
while($row = mysqli_fetch_assoc($qBobot)) { $bobot[$row['id_kriteria']] = $row['nilai_bobot']; }

// Ambil Nilai Mentah
$matriks_x = [];
$qNilai = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_user = '$id_user'");
while($row = mysqli_fetch_assoc($qNilai)){
    $matriks_x[$row['id_alternatif']][$row['id_kriteria']] = $row['nilai'];
}

// B. PERHITUNGAN TOPSIS
// 1. Pembagi (Normalisasi)
$pembagi = [];
foreach($kriteria as $id_k => $k){
    $sum_sq = 0;
    foreach($alternatif as $id_a => $a){
        $val = $matriks_x[$id_a][$id_k] ?? 0;
        $sum_sq += pow($val, 2);
    }
    $pembagi[$id_k] = sqrt($sum_sq);
}

// 2. Matriks R (Normalisasi) & Y (Terbobot)
$matriks_r = [];
$matriks_y = [];
foreach($alternatif as $id_a => $a){
    foreach($kriteria as $id_k => $k){
        // R
        $val = $matriks_x[$id_a][$id_k] ?? 0;
        $r = ($pembagi[$id_k] > 0) ? $val / $pembagi[$id_k] : 0;
        $matriks_r[$id_a][$id_k] = $r;
        
        // Y
        $w = $bobot[$id_k] ?? 0;
        $matriks_y[$id_a][$id_k] = $r * $w;
    }
}

// 3. Solusi Ideal (A+ dan A-)
$solusi_plus = [];
$solusi_min = [];
foreach($kriteria as $id_k => $k){
    $col_values = array_column($matriks_y, $id_k);
    if($k['atribut'] == 'benefit'){
        $solusi_plus[$id_k] = max($col_values);
        $solusi_min[$id_k] = min($col_values);
    } else { // Cost
        $solusi_plus[$id_k] = min($col_values);
        $solusi_min[$id_k] = max($col_values);
    }
}

// 4. Jarak Solusi (D+ dan D-) & Preferensi (V)
$hasil_akhir = [];
foreach($alternatif as $id_a => $a){
    $d_plus = 0;
    $d_min = 0;
    foreach($kriteria as $id_k => $k){
        $y = $matriks_y[$id_a][$id_k];
        $d_plus += pow($y - $solusi_plus[$id_k], 2);
        $d_min += pow($y - $solusi_min[$id_k], 2);
    }
    $d_plus = sqrt($d_plus);
    $d_min = sqrt($d_min);
    
    // Nilai V
    $v = ($d_min + $d_plus > 0) ? $d_min / ($d_min + $d_plus) : 0;
    
    $hasil_akhir[] = [
        'id' => $id_a,
        'nama' => $a['nama_wisata'],
        'nilai' => $v,
        'd_plus' => $d_plus,
        'd_min' => $d_min
    ];
}
$data_jarak = $hasil_akhir;
// Sort Ranking (Nilai tertinggi di atas)
usort($hasil_akhir, function($a, $b) {
    return $b['nilai'] <=> $a['nilai'];
});
?>

<style>
    /* Styling Container Ranking */
    .ranking-wrapper {
        border: 1px solid #22c55e; /* Border Hijau Tipis luar */
        border-radius: 12px;
        padding: 20px;
        background: #fff;
        margin-bottom: 30px;
    }
    
    .ranking-header {
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ranking-header i { color: #d97706; }

    /* Card Item Ranking */
    .rank-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        transition: transform 0.2s;
    }

    /* Khusus Peringkat 1 (Kuning) */
    .rank-card.rank-1 {
        background-color: #fff9c4; /* Kuning Muda */
        border: 1px solid #fdd835; /* Border Kuning Emas */
    }

    /* Lingkaran Nomor Peringkat */
    .rank-circle {
        width: 45px; height: 45px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; color: white;
        margin-right: 20px;
        font-size: 1.1rem;
    }
    .rank-1 .rank-circle { background-color: #fbc02d; } /* Emas */
    .rank-2 .rank-circle { background-color: #9ca3af; } /* Abu */
    .rank-3 .rank-circle { background-color: #ea580c; } /* Perunggu */
    .rank-other .rank-circle { background-color: #cbd5e1; color: #64748b; }

    /* Badge Label Peringkat di Kanan */
    .rank-label {
        margin-left: auto;
        font-size: 0.8rem;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
    }
    .rank-1 .rank-label { background-color: #fbc02d; color: white; }

    /* --- STYLE NAVIGASI PILL (Mirip Screenshot) --- */
    
    /* Wadah Navigasi (Background Abu-abu) */
    .nav-segment {
        background-color: #f1f5f9; /* Abu-abu soft */
        padding: 6px; /* Padding agar tombol putih tidak mepet pinggir */
        border-radius: 50px; /* Membuat sudut sangat bulat (Pill) */
        display: flex; /* Menggunakan Flexbox */
        border: none;
        gap: 5px; /* Jarak antar tombol */
    }

    /* Tombol Biasa (State Tidak Aktif) */
    .nav-segment .nav-link {
        color: #475569; /* Teks Abu-abu Gelap */
        font-weight: 500;
        border: none;
        border-radius: 40px; /* Sudut bulat mengikuti wadah */
        padding: 10px 20px;
        transition: all 0.3s ease;
        flex-grow: 1; /* Agar lebar tombol terbagi rata */
        text-align: center;
        background: transparent;
    }
    
    /* Efek Hover */
    .nav-segment .nav-link:hover {
        color: #1e293b;
        background-color: rgba(255,255,255,0.5);
    }

    /* Tombol Aktif (State Terpilih - Putih & Shadow) */
    .nav-segment .nav-link.active {
        background-color: #ffffff; /* Background Putih */
        color: #0f172a; /* Teks Hitam/Gelap */
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08); /* Efek Bayangan Halus */
    }
    
    .tab-content {
        background: white;
        padding: 25px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        border-top-left-radius: 0; /* Biar nyambung sama tab */
    }

    /* Header Tabel Biru */
    .table-blue thead th {
        background-color: #0d6efd; /* Biru Bootstrap Primary */
        color: white;
        text-align: center;
        font-weight: 500;
        border: none;
        padding: 12px;
    }
    .table-blue tbody td {
        text-align: center;
        vertical-align: middle;
        padding: 12px;
        border-color: #f1f5f9;
        font-size: 0.9rem;
    }
    .table-blue tbody tr:first-child { text-align: left; padding-left: 20px; } /* Nama Alternatif Kiri */
    .table-blue tbody tr td:first-child { text-align: left; font-weight: 500; }
    /* --- STYLE TABEL HIJAU (Untuk Matriks Y) --- */
    .table-green thead th {
        background-color: #10b981; /* Hijau Emerald */
        color: white;
        text-align: center;
        font-weight: 600;
        border: none;
        padding: 12px;
        text-transform: uppercase; /* Agar huruf besar semua seperti di gambar */
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table-green tbody td {
        text-align: center;
        vertical-align: middle;
        padding: 12px;
        border-color: #f1f5f9;
        font-size: 0.9rem;
    }

    /* Rata Kiri untuk Nama Alternatif */
    .table-green tbody td:first-child {
        text-align: left;
        padding-left: 20px;
        font-weight: 500;
    }
    /* --- STYLE SOLUSI IDEAL (BOX VIEW) --- */
    .ideal-section-title {
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1rem;
    }
    .text-positive { color: #059669; } /* Hijau */
    .text-negative { color: #dc2626; } /* Merah */

    .ideal-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        margin-bottom: 12px;
        border-radius: 8px; /* Sudut tumpul */
        font-size: 0.9rem;
        font-weight: 500;
        transition: transform 0.2s;
    }
    
    .ideal-box:hover { transform: translateX(5px); }

    /* Warna Box Positif (Hijau Muda) */
    .ideal-box.positive {
        background-color: #ecfdf5; /* Emerald 50 */
        color: #065f46;
    }

    /* Warna Box Negatif (Merah Muda) */
    .ideal-box.negative {
        background-color: #fef2f2; /* Red 50 */
        color: #991b1b;
    }
</style>

<div class="mb-4">
    <h3 class="fw-light mb-1">Hasil Perhitungan Individual</h3>
    <p class="text-muted">Hasil perhitungan metode TOPSIS untuk penilaian Anda</p>
</div>

<div class="ranking-wrapper">
    <div class="ranking-header">
        <i class="bi bi-trophy-fill"></i> Peringkat Akhir (Nilai Preferensi V)
    </div>

    <?php 
    $rank = 1;
    foreach($hasil_akhir as $h): 
        $isTop = ($rank == 1);
        $circleClass = ($rank == 1) ? 'rank-1' : ( ($rank == 2) ? 'rank-2' : ( ($rank==3)?'rank-3':'rank-other' ) );
        $cardClass = ($rank == 1) ? 'rank-1' : '';
    ?>
    <div class="rank-card <?= $cardClass ?>">
        <div class="rank-circle"><?= $rank ?></div>
        
        <div>
            <h5 class="fw-light mb-0 text-dark"><?= $h['nama'] ?></h5>
            <small class="text-muted">Nilai Preferensi: <?= number_format($h['nilai'], 4) ?></small>
        </div>

        <?php if($isTop): ?>
        <div class="rank-label">
            Peringkat 1
        </div>
        <?php endif; ?>
    </div>
    <?php $rank++; endforeach; ?>
</div>

<ul class="nav nav-pills nav-segment mb-4" id="resultTabs" role="tablist">
    <li class="nav-item flex-fill">
        <button class="nav-link active w-100" data-bs-toggle="tab" data-bs-target="#tabR">Matriks Ternormalisasi (R)</button>
    </li>
    <li class="nav-item flex-fill">
        <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#tabY">Matriks Terbobot (Y)</button>
    </li>
    <li class="nav-item flex-fill">
        <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#tabIdeal">Solusi Ideal</button>
    </li>
    <li class="nav-item flex-fill">
        <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#tabDist">Jarak Solusi</button>
    </li>
</ul>

<div class="tab-content" id="resultTabContent">
    
    <div class="tab-pane fade show active" id="tabR">
        <h6 class="fw-light mb-1">Matriks Ternormalisasi (R)</h6>
        <p class="text-muted small mb-3">Normalisasi menggunakan metode Euclidean</p>
        <div class="table-responsive">
            <table class="table table-bordered table-blue mb-0">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <?php foreach($kriteria as $k) echo "<th>{$k['nama_kriteria']}</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($alternatif as $id_a => $a): ?>
                    <tr>
                        <td><?= $a['nama_wisata'] ?></td>
                        <?php foreach($kriteria as $id_k => $k) echo "<td>".number_format($matriks_r[$id_a][$id_k], 4)."</td>"; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade" id="tabY">
        <h6 class="fw-light mb-3">Matriks Ternormalisasi Terbobot (Y)</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-green mb-0">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <?php foreach($kriteria as $k) echo "<th>{$k['nama_kriteria']}</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($alternatif as $id_a => $a): ?>
                    <tr>
                        <td><?= $a['nama_wisata'] ?></td>
                        <?php foreach($kriteria as $id_k => $k) echo "<td>".number_format($matriks_y[$id_a][$id_k], 4)."</td>"; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade" id="tabIdeal">
        
        <div class="mb-4">
            <h6 class="fw-light mb-1 text-dark">Solusi Ideal Positif (A+) dan Negatif (A-)</h6>
            <p class="text-muted small mb-0">Nilai terbaik dan terburuk untuk setiap kriteria</p>
        </div>

        <div class="row g-4">
            
            <div class="col-md-6">
                <h6 class="ideal-section-title text-positive">Solusi Ideal Positif (A+)</h6>
                <?php foreach($kriteria as $id_k => $k): ?>
                    <div class="ideal-box positive">
                        <span><?= $k['nama_kriteria'] ?></span>
                        <span class="fw-light"><?= number_format($solusi_plus[$id_k], 4) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="col-md-6">
                <h6 class="ideal-section-title text-negative">Solusi Ideal Negatif (A-)</h6>
                <?php foreach($kriteria as $id_k => $k): ?>
                    <div class="ideal-box negative">
                        <span><?= $k['nama_kriteria'] ?></span>
                        <span class="fw-light"><?= number_format($solusi_min[$id_k], 4) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <div class="tab-pane fade" id="tabDist">
        <h6 class="fw-light mb-3">Jarak dari Solusi Ideal (D+ dan D-)</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-blue mb-0">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <th>Jarak Positif (D+)</th>
                        <th>Jarak Negatif (D-)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data_jarak as $res): ?>
                    <tr>
                        <td class="text-start"><?= $res['nama'] ?></td>
                        <td><?= number_format($res['d_plus'], 4) ?></td>
                        <td><?= number_format($res['d_min'], 4) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>