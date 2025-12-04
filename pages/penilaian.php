<style>
    /* --- CSS KHUSUS HALAMAN INI (FIXED VERSION) --- */
    
    /* Judul Halaman */
    .page-title {
        color: #9333ea;
        font-weight: 600;
        font-size: 1.5rem;
    }

    /* Container Card Utama */
    .card-section {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f3f4f6;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 25px;
        margin-bottom: 25px;
    }

    /* Input Bobot */
    .input-soft {
        background-color: #f3f4f6;
        border: none;
        border-radius: 12px;
        padding: 12px 15px;
        font-weight: 500;
        color: #333;
    }
    .input-soft:focus {
        background-color: #fff;
        box-shadow: 0 0 0 2px #d8b4fe;
    }

    /* Badges */
    .badge-attr {
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 6px;
        margin-left: 8px;
        font-weight: 600;
    }
    .badge-cost { background-color: #fce7f3; color: #db2777; }
    .badge-benefit { background-color: #dbeafe; color: #2563eb; }

    /* Bar Total Bobot */
    .total-bobot-bar {
        background-color: #f0f9ff;
        border-radius: 12px;
        padding: 15px;
        color: #0369a1;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }

    /* Legenda */
    .legend-box {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-right: 5px;
        margin-bottom: 15px;
    }

    /* --- PERBAIKAN TABEL (AGAR TIDAK GERAK) --- */
    .table-custom {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        
        /* KUNCI UTAMA: Agar lebar kolom dikunci fix */
        table-layout: fixed; 
    }
    
    .table-custom thead th {
        color: white;
        padding: 15px;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        border: none;
        white-space: nowrap; /* Header satu baris */
    }

    /* Kolom 1: Alternatif (Ungu & Lebar 30%) */
    .table-custom thead th {
        background-color: #a855f7; /* Ungu */
        color: white;
    }

    .table-custom tbody td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
        /* Biar teks panjang di nama wisata turun ke bawah (wrap) */
        word-wrap: break-word; 
    }
    
    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    /* --- CSS CUSTOM DROPDOWN --- */
    .select-custom { display: none; }

    .custom-select-wrapper {
        position: relative;
        user-select: none;
        width: 100%;
    }

    .custom-select-trigger {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 15px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #333;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
        
        /* BIAR GAK DORONG TABEL KALAU TEKS KEPANJANGAN */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .custom-select-trigger:after {
        content: '\F282';
        font-family: 'bootstrap-icons';
        font-size: 0.7rem;
        color: #9ca3af;
        transition: transform 0.3s;
        margin-left: 8px; /* Jarak dikit dari teks */
        flex-shrink: 0;   /* Panah jangan kegencet */
    }

    .custom-select-wrapper.open .custom-select-trigger {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
    }
    .custom-select-wrapper.open .custom-select-trigger:after {
        transform: rotate(180deg);
    }

    .custom-options {
        position: absolute;
        display: block;
        top: 110%; left: 0; right: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 1px solid #f3f4f6;
        z-index: 99;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-10px);
        transition: all 0.2s ease;
        max-height: 200px;
        overflow-y: auto;
    }

    .custom-select-wrapper.open .custom-options {
        opacity: 1; visibility: visible; pointer-events: all; transform: translateY(0);
    }

    .custom-option {
        padding: 10px 15px;
        font-size: 0.9rem;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid #f9fafb;
    }
    .custom-option:last-child { border-bottom: none; }
    
    .custom-option:hover {
        background-color: #e0f2fe; /* Biru muda hover */
        color: #0ea5e9;
    }
    .custom-option.selected {
        background-color: #06b6d4; /* Cyan Selected */
        color: white;
    }

    /* Tombol Simpan */
    .btn-orange {
        background: linear-gradient(90deg, #ea580c 0%, #f97316 100%);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(234, 88, 12, 0.3);
    }
    .btn-orange:hover {
        background: linear-gradient(90deg, #c2410c 0%, #ea580c 100%);
        color: white;
    }
</style>

<div class="mb-4">
    <h2 class="page-title mb-1">Penilaian Individual</h2>
    <p class="text-muted">Masukkan bobot preferensi dan nilai penilaian Anda menggunakan metode TOPSIS</p>
</div>

<form action="process/simpan_penilaian.php" method="POST">
    
    <div class="card-section">
        <h6 class="fw-light mb-3 text-dark">1. Input Bobot Kriteria</h6>
        <p class="text-muted small mb-4">Tentukan tingkat kepentingan setiap kriteria (Total harus 1.0 atau 100%)</p>

        <div class="row g-4">
            <?php
            // PHP LOGIC ASLI (JANGAN DIUBAH)
            $queryKriteria = mysqli_query($conn, "SELECT * FROM kriteria");
            while($k = mysqli_fetch_array($queryKriteria)) {
            ?>
            <div class="col-md-6 col-lg-6"> <label class="form-label d-flex align-items-center fw-light text-dark mb-2">
                    <?= $k['nama_kriteria'] ?> 
                    <span class="badge-attr <?= ($k['atribut']=='cost')?'badge-cost':'badge-benefit'; ?>">
                        <?= ucfirst($k['atribut']) ?>
                    </span>
                </label>
                <input type="number" step="0.01" name="bobot[<?= $k['id_kriteria'] ?>]" 
                       class="form-control input-soft bobot-input" 
                       placeholder="0.0" required>
            </div>
            <?php } ?>
        </div>

        <div class="total-bobot-bar">
            <span>Total Bobot:</span>
            <span class="d-flex align-items-center">
                <span id="total-value">0.00</span> 
                <i class="bi bi-exclamation-circle-fill ms-2 text-warning" id="total-icon"></i>
            </span>
        </div>
    </div>

    <div class="card-section">
        <h6 class="fw-light mb-2 text-dark">2. Matriks Penilaian (Skala Likert 1-5)</h6>
        <p class="text-muted small mb-3">Berikan penilaian untuk setiap alternatif pada masing-masing kriteria</p>

        <div class="mb-4">
            <span class="legend-box" style="background:#fee2e2; color:#ef4444;">1 = Sangat Buruk</span>
            <span class="legend-box" style="background:#ffedd5; color:#f97316;">2 = Buruk</span>
            <span class="legend-box" style="background:#fef9c3; color:#eab308;">3 = Cukup</span>
            <span class="legend-box" style="background:#dbeafe; color:#3b82f6;">4 = Baik</span>
            <span class="legend-box" style="background:#d1fae5; color:#10b981;">5 = Sangat Baik</span>
        </div>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <?php 
                        mysqli_data_seek($queryKriteria, 0); 
                        while($k = mysqli_fetch_array($queryKriteria)) { 
                            echo "<th>{$k['nama_kriteria']}</th>"; 
                        } 
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $queryAlt = mysqli_query($conn, "SELECT * FROM alternatif");
                    while($a = mysqli_fetch_array($queryAlt)){
                    ?>
                    <tr>
                        <td class="fw-light text-dark ps-4">
                            <?= $a['nama_wisata'] ?>
                            <div class="text-muted fw-normal small" style="font-size: 0.75rem;"><?= $a['lokasi'] ?? '-' ?></div>
                        </td>
                        <?php 
                        mysqli_data_seek($queryKriteria, 0);
                        while($k = mysqli_fetch_array($queryKriteria)) { 
                        ?>
                        <td>
                            <select name="nilai[<?= $a['id_alternatif'] ?>][<?= $k['id_kriteria'] ?>]" class="form-select select-custom">
                            <option value="1">1 - Sangat Buruk</option>    
                                <option value="2">2 - Buruk</option>
                                <option value="3">3 - Cukup</option>
                                <option value="4">4 - Baik</option>
                                <option value="5">5 - Sangat Baik</option>
                            </select>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-5">
        <button type="submit" class="btn btn-orange">
            Simpan & Hitung TOPSIS
        </button>
    </div>

</form>


<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Definisi Variabel
    const inputs = document.querySelectorAll('.bobot-input'); // Ambil input yang ada class bobot-input
    const totalValueSpan = document.getElementById('total-value');
    const totalIcon = document.getElementById('total-icon');
    const form = document.querySelector('form');

    // 2. Fungsi Hitung
    function hitungTotal() {
        let total = 0;
        inputs.forEach(input => {
            let val = parseFloat(input.value) || 0;
            total += val;
        });

        totalValueSpan.innerText = total.toFixed(2);

        // Validasi: Harus mendekati 1.0 atau 100
        if (Math.abs(total - 1.0) < 0.001 || Math.abs(total - 100) < 0.1) {
            totalValueSpan.style.color = '#10b981'; // Hijau
            totalIcon.className = 'bi bi-check-circle-fill ms-2 text-success';
            return true;
        } else {
            totalValueSpan.style.color = '#ef4444'; // Merah
            totalIcon.className = 'bi bi-exclamation-circle-fill ms-2 text-danger';
            return false;
        }
    }

    // 3. Listener Realtime
    inputs.forEach(input => {
        input.addEventListener('input', hitungTotal);
    });

    // 4. Cegah Submit jika salah
    form.addEventListener('submit', function(e) {
        if (!hitungTotal()) {
            e.preventDefault();
            alert('⚠️ Perhatian!\nTotal bobot harus bernilai 1.00 (100%).\nSilakan perbaiki inputan Anda.');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Jalankan sekali di awal
    hitungTotal();
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // Cari semua elemen select dengan class 'select-custom'
    const selects = document.querySelectorAll('.select-custom');

    selects.forEach(select => {
        // 1. Buat Wrapper
        const wrapper = document.createElement('div');
        wrapper.classList.add('custom-select-wrapper');
        
        // 2. Buat Trigger (Tampilan Awal)
        const trigger = document.createElement('div');
        trigger.classList.add('custom-select-trigger');
        // Ambil teks dari option yang sedang terpilih (selected)
        trigger.textContent = select.options[select.selectedIndex].text;
        
        // 3. Buat Container Options
        const optionsContainer = document.createElement('div');
        optionsContainer.classList.add('custom-options');

        // 4. Loop setiap <option> di select asli untuk dibuat versi cantiknya
        Array.from(select.options).forEach(option => {
            const customOption = document.createElement('div');
            customOption.classList.add('custom-option');
            customOption.dataset.value = option.value;
            customOption.textContent = option.text;

            // Jika option ini terpilih di awal
            if (option.selected) {
                customOption.classList.add('selected');
            }

            // EVENT KLIK PADA OPSI
            customOption.addEventListener('click', function() {
                // Update tampilan trigger
                trigger.textContent = this.textContent;
                
                // Update select ASLI di belakang layar
                select.value = this.dataset.value;
                
                // Reset kelas selected
                optionsContainer.querySelectorAll('.custom-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                
                // Tutup dropdown
                wrapper.classList.remove('open');
            });

            optionsContainer.appendChild(customOption);
        });

        // Masukkan elemen ke DOM
        wrapper.appendChild(trigger);
        wrapper.appendChild(optionsContainer);
        
        // Sisipkan wrapper SETELAH select asli
        select.parentNode.insertBefore(wrapper, select.nextSibling);

        // EVENT BUKA TUTUP DROPDOWN
        trigger.addEventListener('click', function(e) {
            // Tutup dropdown lain jika ada yang terbuka
            document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                if (w !== wrapper) w.classList.remove('open');
            });
            wrapper.classList.toggle('open');
            e.stopPropagation(); // Mencegah event bubbling ke window
        });
    });

    // Event Klik di luar untuk menutup dropdown
    window.addEventListener('click', function() {
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            wrapper.classList.remove('open');
        });
    });

});
</script>