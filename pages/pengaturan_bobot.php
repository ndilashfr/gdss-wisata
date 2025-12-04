<?php
// Pastikan hanya ADMIN yang bisa akses
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
    exit;
}

// 1. AMBIL DATA KRITERIA
$kriteria = [];
$qK = mysqli_query($conn, "SELECT * FROM kriteria");
while($row = mysqli_fetch_assoc($qK)){ $kriteria[] = $row; }

// 2. LOGIKA SIMPAN (Handle POST)
if(isset($_POST['simpan_bobot'])){
    $target_role = $_POST['target_role']; 
    $bobot_input = $_POST['bobot']; 

    // Cari ID User
    $qUser = mysqli_query($conn, "SELECT id_user FROM users WHERE role='$target_role'");
    $uData = mysqli_fetch_assoc($qUser);
    
    if($uData){
        $id_user = $uData['id_user'];
        mysqli_query($conn, "DELETE FROM bobot_user WHERE id_user='$id_user'");
        
        foreach($bobot_input as $id_k => $val){
            $val = floatval($val);
            mysqli_query($conn, "INSERT INTO bobot_user (id_user, id_kriteria, nilai_bobot) VALUES ('$id_user', '$id_k', '$val')");
        }
        echo "<script>alert('Berhasil menyimpan bobot untuk ".ucfirst($target_role)."!'); window.location.href='index.php?page=pengaturan_bobot&tab=$target_role';</script>";
    }
}

// 3. KONFIGURASI TABS
$dms = [
    'kadispar' => ['label' => 'DM1: Kadispar', 'color' => 'purple'],
    'phri'     => ['label' => 'DM2: PHRI',     'color' => 'cyan'],
    'akademisi'=> ['label' => 'DM3: Akademisi', 'color' => 'pink']
];
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'kadispar';
?>

<style>
    /* --- CSS MODERN --- */
    
   /* Wadah Utama (Background Abu-abu) */
   .custom-tab-wrapper {
        background-color: #f1f5f9; /* Abu-abu soft seperti di gambar */
        padding: 6px; /* Padding agar tombol putih tidak mepet pinggir */
        border-radius: 50px; /* Sudut bulat sempurna */
        display: flex;
        gap: 5px; /* Jarak antar tombol */
        margin-bottom: 25px;
    }

    /* Tombol Tab (State Biasa) */
    .custom-tab-btn {
        flex: 1; /* Agar lebar tombol dibagi rata */
        border: none;
        background: transparent;
        color: #475569; /* Teks abu-abu gelap */
        font-weight: 500;
        padding: 12px 20px;
        border-radius: 40px; /* Mengikuti bentuk wadah */
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px; /* Jarak teks dengan ikon */
        cursor: pointer;
        text-decoration: none; /* Hilangkan garis bawah link */
    }

    /* Efek Hover */
    .custom-tab-btn:hover {
        background-color: rgba(255,255,255,0.5);
        color: #1e293b;
    }

    /* State Aktif (Background Putih + Shadow) */
    .custom-tab-btn.active {
        background-color: #ffffff;
        color: #0f172a; /* Hitam pekat */
        font-weight: 700;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08); /* Shadow halus agar 'pop-up' */
    }

    /* Ikon Centang Hijau */
    .icon-check {
        color: #10b981; /* Hijau Emerald */
        font-size: 1.1rem;
    }
    /* Kotak Status Total Bobot */
    .status-box {
        border-radius: 16px;
        padding: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
        transition: all 0.3s;
        border-width: 1px;
        border-style: solid;
    }
    .status-valid {
        background-color: #ecfdf5; /* Hijau Muda Mint */
        border-color: #34d399;     /* Border Hijau */
    }
    .status-valid .icon-area { color: #059669; }
    .status-valid h5 { color: #065f46; }
    
    .status-invalid {
        background-color: #fff7ed; /* Orange Muda */
        border-color: #fdba74;
    }
    .status-invalid .icon-area { color: #ea580c; }
    .status-invalid h5 { color: #9a3412; }

    /* Card Utama dengan Header Gradient */
    .card-gradient-header {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .card-header-custom {
        /* Gradient Ungu ke Biru persis gambar */
        background: linear-gradient(90deg, #a855f7 0%, #06b6d4 100%);
        padding: 20px 25px;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* --- CUSTOM SLIDER CSS (PENTING) --- */
    .slider-wrapper {
        position: relative;
        height: 12px; /* Tebal slider */
        width: 100%;
        display: flex;
        align-items: center;
        margin-top: 5px;
    }

    /* 1. Track Abu-abu (Background) */
    .slider-track {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #e2e8f0;
        border-radius: 10px;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    /* 2. Bar Gradient Warna (Isian) */
    .slider-fill {
        position: absolute;
        top: 0; left: 0; bottom: 0;
        height: 100%;
        border-radius: 10px;
        /* Gradient Ungu ke Biru */
        background: linear-gradient(90deg, #a855f7 0%, #3b82f6 100%);
        width: 0%; /* Diatur JS */
        z-index: 2;
    }

    /* 3. Input Range Asli (Transparan di atas) */
    .slider-input {
        position: absolute;
        top: -4px; /* Naik dikit biar thumb pas di tengah */
        left: 0;
        width: 100%;
        height: 20px;
        opacity: 0; /* TRANSPARAN SUPAYA FILL YG KELIHATAN */
        z-index: 3;
        cursor: pointer;
        margin: 0;
    }

    /* 4. Thumb (Pentolan) Custom - Perlu CSS khusus biar kelihatan walau input transparan? 
       Triknya: Input opacity 0 menghilang, jadi kita butuh thumb palsu atau styling khusus.
       Cara lebih mudah: Opacity input JANGAN 0, tapi track-nya yang transparan. */
    
    /* REVISI STRATEGI SLIDER: */
    .range-input {
        -webkit-appearance: none;
        width: 100%;
        height: 12px;
        border-radius: 10px;
        background: transparent; /* Track transparan */
        outline: none;
        position: absolute;
        top: 0; left: 0;
        z-index: 5;
        margin: 0;
    }
    
    /* Style Thumb (Pentolan) */
    .range-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #8b5cf6; /* Warna Ungu */
        border: 4px solid #ffffff; /* Ring Putih */
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        cursor: pointer;
        margin-top: -6px; /* Center vertikal */
    }

    .badge-soft {
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 10px;
    }
    .badge-cost { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    .badge-benefit { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }

    .input-box-custom {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-weight: 600;
        text-align: center;
        height: 45px;
    }
    .input-box-custom:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }
</style>

<div class="mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-gradient-to-r p-3 rounded-4 text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
            <i class="bi bi-sliders2 fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">Pengaturan Bobot per Decision Maker</h3>
            <p class="text-muted mb-0">Atur bobot kriteria yang berbeda untuk setiap Decision Maker (total harus = 1.0000)</p>
        </div>
    </div>
</div>
<div class="custom-tab-wrapper">
    <?php foreach($dms as $role => $data): ?>
        <a href="index.php?page=pengaturan_bobot&tab=<?= $role ?>" 
           class="custom-tab-btn <?= ($active_tab==$role)?'active':'' ?>">
            
            <?= $data['label'] ?>
            
            <i class="bi bi-check-circle icon-check"></i>
        </a>
    <?php endforeach; ?>
</div>

<form method="POST" id="formBobot">
    <input type="hidden" name="target_role" value="<?= $active_tab ?>">

    <div class="status-box status-valid" id="statusBox">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-check-circle-fill fs-2 icon-area" id="statusIcon"></i>
            <div>
                <h5 class="mb-0 fw-light" id="statusTitle">
                    Total Bobot <?= ucfirst($active_tab) ?>: <span id="totalDisplay">1.0000</span>
                </h5>
                <small class="text-muted" id="statusDesc">Bobot sudah valid, siap disimpan</small>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary rounded-pill px-3" onclick="distribusiMerata()">
                Distribusi Merata
            </button>
            <?php foreach($dms as $r => $d): if($r == $active_tab) continue; ?>
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" onclick="salinDari('<?= $r ?>')">
                    Salin dari <?= ucfirst($r) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card card-gradient-header">
        <div class="card-header-custom">
            <i class="bi bi-sliders"></i> Atur Bobot untuk <?= ucfirst($active_tab) ?>
        </div>
        
        <div class="card-body p-4 bg-white">
            
            <?php 
            // Ambil bobot tersimpan
            $qU = mysqli_query($conn, "SELECT id_user FROM users WHERE role='$active_tab'");
            $uD = mysqli_fetch_assoc($qU);
            $uid = $uD['id_user'] ?? 0;

            $saved_weights = [];
            $qW = mysqli_query($conn, "SELECT * FROM bobot_user WHERE id_user='$uid'");
            while($w = mysqli_fetch_assoc($qW)){ $saved_weights[$w['id_kriteria']] = $w['nilai_bobot']; }

            foreach($kriteria as $idx => $k): 
                $val = isset($saved_weights[$k['id_kriteria']]) ? $saved_weights[$k['id_kriteria']] : 0;
            ?>
            
            <div class="row align-items-center mb-4 p-3 rounded-3 border" style="background-color: #fafafa;">
                
                <div class="col-12 mb-2">
                    <div class="d-flex align-items-center">
                        <span class="fw-bold text-dark fs-6 me-2">C<?= $idx+1 ?>: <?= $k['nama_kriteria'] ?></span>
                        <span class="badge-soft <?= ($k['atribut']=='cost')?'badge-cost':'badge-benefit' ?>">
                            <?= ucfirst($k['atribut']) ?>
                        </span>
                    </div>
                </div>

                <div class="col-md-2 col-3">
                    <input type="number" step="0.0001" min="0" max="1" 
                           name="bobot[<?= $k['id_kriteria'] ?>]" 
                           id="input-<?= $k['id_kriteria'] ?>"
                           class="form-control input-box-custom input-bobot" 
                           value="<?= $val ?>"
                           oninput="syncSlider(this, '<?= $k['id_kriteria'] ?>')">
                </div>

                <div class="col-md-8 col-7">
                    <div class="slider-wrapper">
                        <div class="slider-track"></div>
                        <div class="slider-fill" id="fill-<?= $k['id_kriteria'] ?>" style="width: <?= $val*100 ?>%;"></div>
                        <input type="range" min="0" max="1" step="0.0001" 
                               id="slider-<?= $k['id_kriteria'] ?>"
                               value="<?= $val ?>"
                               class="range-input"
                               oninput="syncInput(this, '<?= $k['id_kriteria'] ?>')">
                    </div>
                </div>

                <div class="col-md-2 col-2 text-end">
                    <span class="fw-bold text-secondary small" id="perc-<?= $k['id_kriteria'] ?>">
                        <?= number_format($val*100, 2) ?>%
                    </span>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" name="simpan_bobot" id="btnSimpan" class="btn btn-success px-5 py-2 rounded-pill fw-bold shadow" disabled>
                    Simpan Bobot <?= ucfirst($active_tab) ?>
                </button>
            </div>

        </div>
    </div>
</form>

<?php
$all_weights = [];
foreach($dms as $role => $d){
    $qUx = mysqli_query($conn, "SELECT id_user FROM users WHERE role='$role'");
    $uDx = mysqli_fetch_assoc($qUx);
    $uidx = $uDx['id_user'] ?? 0;
    $wArr = [];
    $qWx = mysqli_query($conn, "SELECT * FROM bobot_user WHERE id_user='$uidx'");
    while($wx = mysqli_fetch_assoc($qWx)){ $wArr[$wx['id_kriteria']] = $wx['nilai_bobot']; }
    $all_weights[$role] = $wArr;
}
?>

<script>
    const allWeights = <?= json_encode($all_weights) ?>;
    const totalKriteria = <?= count($kriteria) ?>;

    function syncSlider(el, id) {
        let val = parseFloat(el.value) || 0;
        if(val > 1) val = 1;
        document.getElementById('slider-' + id).value = val;
        updateUI(id, val);
    }

    function syncInput(el, id) {
        let val = parseFloat(el.value) || 0;
        document.getElementById('input-' + id).value = val.toFixed(4);
        updateUI(id, val);
    }

    function updateUI(id, val) {
        // Update lebar Fill Gradient
        document.getElementById('fill-' + id).style.width = (val * 100) + '%';
        // Update teks persen
        document.getElementById('perc-' + id).innerText = (val * 100).toFixed(2) + '%';
        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.input-bobot').forEach(inp => total += parseFloat(inp.value) || 0);

        // Update Text
        document.getElementById('totalDisplay').innerText = total.toFixed(4);

        const box = document.getElementById('statusBox');
        const icon = document.getElementById('statusIcon');
        const title = document.getElementById('statusTitle');
        const desc = document.getElementById('statusDesc');
        const btn = document.getElementById('btnSimpan');

        // Toleransi 0.001 karena floating point
        if (Math.abs(total - 1.0) < 0.001) {
            box.className = 'status-box status-valid';
            icon.className = 'bi bi-check-circle-fill fs-2 icon-area';
            desc.innerText = 'Bobot sudah valid, siap disimpan';
            btn.disabled = false;
        } else {
            box.className = 'status-box status-invalid';
            icon.className = 'bi bi-exclamation-circle-fill fs-2 icon-area';
            desc.innerText = 'Sesuaikan bobot agar total = 1.0000';
            btn.disabled = true;
        }
    }

    function distribusiMerata() {
        const val = (1 / totalKriteria);
        document.querySelectorAll('.input-bobot').forEach(inp => {
            const id = inp.id.split('-')[1];
            inp.value = val.toFixed(4);
            document.getElementById('slider-' + id).value = val;
            updateUI(id, val);
        });
    }

    function salinDari(role) {
        const data = allWeights[role];
        if(!data || Object.keys(data).length === 0) {
            alert('Data bobot kosong untuk ' + role); return;
        }
        for (const [id, val] of Object.entries(data)) {
            const el = document.getElementById('input-' + id);
            if(el) {
                el.value = val;
                document.getElementById('slider-' + id).value = val;
                updateUI(id, parseFloat(val));
            }
        }
    }

    // Init
    document.addEventListener("DOMContentLoaded", hitungTotal);
</script>