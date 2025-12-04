<?php
// Proteksi: Hanya Admin
if($_SESSION['role'] != 'admin'){
    echo "<script>alert('Akses ditolak!'); window.location='index.php';</script>";
    exit;
}

// --- LOGIKA PHP (CRUD - TIDAK BERUBAH) ---
if(isset($_POST['tambah_alternatif'])){
    $nama = $_POST['nama_wisata'];
    $lokasi = $_POST['lokasi'];
    mysqli_query($conn, "INSERT INTO alternatif (nama_wisata, lokasi) VALUES ('$nama', '$lokasi')");
    echo "<meta http-equiv='refresh' content='0'>";
}
if(isset($_POST['tambah_kriteria'])){
    $kode = $_POST['kode'];
    $nama = $_POST['nama_kriteria'];
    $atribut = $_POST['atribut'];
    mysqli_query($conn, "INSERT INTO kriteria (kode, nama_kriteria, atribut) VALUES ('$kode', '$nama', '$atribut')");
    echo "<meta http-equiv='refresh' content='0'>";
}
if(isset($_GET['hapus_alt'])){
    $id = $_GET['hapus_alt'];
    mysqli_query($conn, "DELETE FROM alternatif WHERE id_alternatif='$id'");
    echo "<script>window.location='index.php?page=master_data';</script>";
}
if(isset($_GET['hapus_krit'])){
    $id = $_GET['hapus_krit'];
    mysqli_query($conn, "DELETE FROM kriteria WHERE id_kriteria='$id'");
    echo "<script>window.location='index.php?page=master_data';</script>";
}
if(isset($_POST['edit_alternatif'])){
    $id = $_POST['id_alternatif'];
    $nama = $_POST['nama_wisata'];
    $lokasi = $_POST['lokasi'];
    mysqli_query($conn, "UPDATE alternatif SET nama_wisata='$nama', lokasi='$lokasi' WHERE id_alternatif='$id'");
    echo "<meta http-equiv='refresh' content='0'>";
}
if(isset($_POST['edit_kriteria'])){
    $id = $_POST['id_kriteria'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama_kriteria'];
    $atribut = $_POST['atribut'];
    mysqli_query($conn, "UPDATE kriteria SET kode='$kode', nama_kriteria='$nama', atribut='$atribut' WHERE id_kriteria='$id'");
    echo "<meta http-equiv='refresh' content='0'>";
}
?>

<style>
    /* Judul Halaman Ungu */
    .page-title {
        color: #9333ea; /* Ungu */
        font-weight: 600;
        font-size: 1.75rem;
        margin-bottom: 5px;
    }
    .page-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
        margin-bottom: 25px;
    }
/* Style Tabs Segmented (Toggle dalam Wadah Abu) */
.nav-pills-custom {
        background-color: #f1f5f9; /* Wadah Abu-abu */
        padding: 5px; /* Jarak antara wadah dan tombol putih */
        border-radius: 50px; /* Sudut bulat wadah */
        display: inline-flex; /* Agar wadah tidak lebar full */
        gap: 0; /* Tidak ada jarak antar elemen (diatur padding) */
    }

    .nav-pills-custom .nav-link {
        color: #64748b; /* Warna teks tidak aktif (Abu gelap) */
        font-weight: 500;
        padding: 8px 24px; /* Ukuran tombol */
        border-radius: 40px; /* Sudut bulat tombol */
        border: none;
        background: transparent; /* Transparan defaultnya */
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .nav-pills-custom .nav-link:hover {
        color: #333;
    }

    /* Style Tombol Aktif (Putih di dalam Abu) */
    .nav-pills-custom .nav-link.active {
        background-color: #ffffff; /* Background Putih */
        color: #0f172a; /* Teks Hitam/Gelap */
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); /* Shadow halus */
    }
    /* Card Putih Bersih */
    .card-clean {
        background: white;
        border-radius: 20px; /* Sudut sangat bulat */
        border: 1px solid #f3f4f6;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02); /* Bayangan sangat halus */
        padding: 30px;
        margin-top: 20px;
    }

    /* Tombol Tambah (Ungu Gradient Kapsul) */
    .btn-add-capsule {
        background: linear-gradient(90deg, #a855f7 0%, #8b5cf6 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 10px 24px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
        transition: transform 0.2s;
    }
    .btn-add-capsule:hover {
        transform: translateY(-2px);
        color: white;
    }

    /* Tabel Minimalis */
    .table-clean {
        width: 100%;
        border-collapse: collapse;
    }
    .table-clean thead th {
        background-color: #f9fafb; /* Abu-abu sangat muda */
        color: #1f2937; /* Hitam soft */
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: capitalize; /* Huruf besar di awal kata */
        padding: 20px;
        border: none;
        text-align: left;
    }
    .table-clean tbody td {
        padding: 20px;
        border-bottom: 1px solid #f3f4f6; /* Garis tipis */
        color: #374151;
        font-size: 0.95rem;
        vertical-align: middle;
    }
    /* Hapus garis border tabel bawaan bootstrap */
    .table-clean>:not(caption)>*>* { border-bottom-width: 0; }
    
    /* Tombol Aksi (Icon Only) */
    .btn-icon-only {
        background: transparent;
        border: none;
        padding: 5px;
        font-size: 1.1rem;
        transition: transform 0.2s;
    }
    .btn-icon-only:hover { transform: scale(1.1); }
    
    .icon-edit { color: #4b5563; } /* Abu-abu Tua (Pensil) */
    .icon-delete { color: #ef4444; } /* Merah (Sampah) */
/* --- STYLE BADGE ATRIBUT (PASTEL) --- */
.badge-soft {
        padding: 6px 14px;
        border-radius: 6px; /* Sudut sedikit membulat (bukan bulat penuh) */
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        display: inline-block;
        min-width: 70px; /* Agar lebar seragam */
        text-align: center;
    }

    /* Warna Cost (Pink Pastel) */
    .badge-cost {
        background-color: #fce7f3; /* Pink background sangat muda */
        color: #9d174d; /* Teks Pink Tua/Merah Maroon */
    }

    /* Warna Benefit (Cyan/Biru Muda Pastel) */
    .badge-benefit {
        background-color: #cffafe; /* Cyan background sangat muda */
        color: #0e7490; /* Teks Cyan Tua/Biru Laut */
    }
</style>

<div>
    <h1 class="page-title">Data Master</h1>
    <p class="page-subtitle">Kelola data alternatif destinasi wisata dan kriteria penilaian</p>
</div>
<ul class="nav nav-pills nav-pills-custom mb-4" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="alt-tab" data-bs-toggle="tab" data-bs-target="#alternatif-pane" type="button">
        Data Alternatif
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="krit-tab" data-bs-toggle="tab" data-bs-target="#kriteria-pane" type="button">
        Data Kriteria
    </button>
  </li>
</ul>

<div class="card-clean">
  <div class="tab-content" id="myTabContent">
  
    <div class="tab-pane fade show active" id="alternatif-pane">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark">Alternatif Destinasi Wisata</h5>
            <button class="btn btn-add-capsule" data-bs-toggle="modal" data-bs-target="#modalAlt">
                <i class="bi bi-plus-lg me-1"></i> Tambah Data
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th style="width: 80px;">No</th>
                        <th>Nama Destinasi Wisata</th>
                        <th>Lokasi</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no=1;
                    $qAlt = mysqli_query($conn, "SELECT * FROM alternatif");
                    while($row = mysqli_fetch_array($qAlt)){
                    ?>
                    <tr>
                        <td class="text-muted fw-bold"><?= $no++ ?></td>
                        <td class="fw-medium text-dark"><?= $row['nama_wisata'] ?></td>
                        <td class="text-secondary"><?= $row['lokasi'] ?></td>
                        <td>
                            <button class="btn-icon-only icon-edit" data-bs-toggle="modal" data-bs-target="#modalEditAlt<?= $row['id_alternatif'] ?>" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <a href="index.php?page=master_data&hapus_alt=<?= $row['id_alternatif'] ?>" class="btn-icon-only icon-delete" onclick="return confirm('Yakin hapus data ini?')" title="Hapus">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditAlt<?= $row['id_alternatif'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <form method="POST">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Alternatif</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_alternatif" value="<?= $row['id_alternatif'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">NAMA DESTINASI</label>
                                            <input type="text" name="nama_wisata" class="form-control" value="<?= $row['nama_wisata'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">LOKASI</label>
                                            <input type="text" name="lokasi" class="form-control" value="<?= $row['lokasi'] ?>">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="submit" name="edit_alternatif" class="btn btn-primary w-100 rounded-pill">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade" id="kriteria-pane">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark">Data Kriteria Penilaian</h5>
            <button class="btn btn-add-capsule" data-bs-toggle="modal" data-bs-target="#modalKrit">
                <i class="bi bi-plus-lg me-1"></i> Tambah Data
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th style="width: 80px;">Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Atribut</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qKrit = mysqli_query($conn, "SELECT * FROM kriteria");
                    while($row = mysqli_fetch_array($qKrit)){
                    ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= $row['kode'] ?></td>
                        <td class="fw-medium text-dark"><?= $row['nama_kriteria'] ?></td>
                        <td>
    <span class="badge-soft <?= ($row['atribut']=='cost') ? 'badge-cost' : 'badge-benefit' ?>">
        <?= ucfirst($row['atribut']) ?>
    </span>
</td>
                        <td>
                            <button class="btn-icon-only icon-edit" data-bs-toggle="modal" data-bs-target="#modalEditKrit<?= $row['id_kriteria'] ?>">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <a href="index.php?page=master_data&hapus_krit=<?= $row['id_kriteria'] ?>" class="btn-icon-only icon-delete" onclick="return confirm('Yakin hapus data ini?')">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditKrit<?= $row['id_kriteria'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <form method="POST">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Kriteria</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_kriteria" value="<?= $row['id_kriteria'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">KODE</label>
                                            <input type="text" name="kode" class="form-control" value="<?= $row['kode'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">NAMA KRITERIA</label>
                                            <input type="text" name="nama_kriteria" class="form-control" value="<?= $row['nama_kriteria'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">ATRIBUT</label>
                                            <select name="atribut" class="form-select">
                                                <option value="benefit" <?= ($row['atribut']=='benefit')?'selected':'' ?>>Benefit</option>
                                                <option value="cost" <?= ($row['atribut']=='cost')?'selected':'' ?>>Cost</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="submit" name="edit_kriteria" class="btn btn-primary w-100 rounded-pill">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

  </div>
</div>

<div class="modal fade" id="modalAlt" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <form method="POST">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">Tambah Wisata Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">NAMA DESTINASI</label>
                <input type="text" name="nama_wisata" class="form-control bg-light border-0" placeholder="Masukkan nama wisata" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">LOKASI</label>
                <input type="text" name="lokasi" class="form-control bg-light border-0" placeholder="Alamat lengkap">
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="submit" name="tambah_alternatif" class="btn btn-add-capsule w-100">Simpan Data</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalKrit" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <form method="POST">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">Tambah Kriteria Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">KODE (C1, C2..)</label>
                <input type="text" name="kode" class="form-control bg-light border-0" placeholder="Contoh: C1" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">NAMA KRITERIA</label>
                <input type="text" name="nama_kriteria" class="form-control bg-light border-0" placeholder="Contoh: Harga Tiket" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">ATRIBUT</label>
                <select name="atribut" class="form-select bg-light border-0">
                    <option value="benefit">Benefit (Semakin tinggi semakin baik)</option>
                    <option value="cost">Cost (Semakin rendah semakin baik)</option>
                </select>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="submit" name="tambah_kriteria" class="btn btn-add-capsule w-100">Simpan Data</button>
          </div>
      </form>
    </div>
  </div>
</div>