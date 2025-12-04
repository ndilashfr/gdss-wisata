<?php
session_start();
include '../config/database.php';

// 1. Cek Login & Request Method
if(!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$bobot_input = $_POST['bobot']; // Array [id_kriteria => nilai]
$nilai_input = $_POST['nilai']; // Array [id_alternatif][id_kriteria] => nilai

// 2. SIMPAN BOBOT PREFERENSI
// Hapus bobot lama user ini dulu (biar bersih/update)
mysqli_query($conn, "DELETE FROM bobot_user WHERE id_user = '$id_user'");

foreach($bobot_input as $id_kriteria => $nilai){
    $nilai = floatval($nilai);
    $q = "INSERT INTO bobot_user (id_user, id_kriteria, nilai_bobot) VALUES ('$id_user', '$id_kriteria', '$nilai')";
    mysqli_query($conn, $q);
}

// 3. SIMPAN NILAI MATRIKS
// Hapus penilaian lama user ini
mysqli_query($conn, "DELETE FROM penilaian WHERE id_user = '$id_user'");

foreach($nilai_input as $id_alternatif => $kriteria_array){
    foreach($kriteria_array as $id_kriteria => $skor){
        $skor = floatval($skor);
        $q = "INSERT INTO penilaian (id_user, id_alternatif, id_kriteria, nilai) 
              VALUES ('$id_user', '$id_alternatif', '$id_kriteria', '$skor')";
        mysqli_query($conn, $q);
    }
}

// 4. REDIRECT KE HALAMAN HASIL (PENTING!)
// Kita harus kembali ke index.php, jangan ke file pages langsung
header("Location: ../index.php?page=hasil_individu");
exit;
?>