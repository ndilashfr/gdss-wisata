<?php
session_start();
// Cek Login sederhana
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include 'config/database.php';
include 'config/functions.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GDSS Wisata Batu</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            background-color: #f8f9fa; /* Background abu-abu sangat muda */
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
        }

        /* Sidebar Wrapper */
        .sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            /* Pastikan class .sidebar di style.css sudah ada background gradasi ungunya */
        }

        /* Main Content Adjustments */
        .main-content {
            margin-left: 16.66667%; /* Offset untuk col-md-2 sidebar */
            min-height: 100vh;
            padding-bottom: 50px;
        }

        /* Top Header sesuai Screenshot */
        .top-header {
            background-color: white;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            /* Garis bawah tipis pudar */
            border-bottom: 1px solid rgba(0,0,0,0.03); 
        }

        .header-title h4 {
            font-weight: 600;
            color: #333; /* Warna "Dashboard" */
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 0.9rem;
            color: #888;
        }
        
        /* Badge Tahun 2025 di kanan */
        .badge-edition {
            background-color: #f3e8ff; /* Ungu sangat muda */
            color: #7c3aed; /* Teks Ungu */
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .sidebar-wrapper { position: relative; height: auto; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-md-2 sidebar sidebar-wrapper d-none d-md-block">
            <?php include 'layout/sidebar.php'; ?>
        </div>

        <div class="col-md-10 main-content">
            
            <div class="top-header">
                <div class="header-title">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="text-muted fs-5">• Selamat Datang,</span>
                        <span class="fs-5 fw-light" style="color: #9333ea;">
                            <?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : ucfirst($_SESSION['role']); ?>
                        </span>
                    </div>
                    <div class="header-subtitle">
                        Sistem Pendukung Keputusan Kelompok - Metode TOPSIS & Borda
                    </div>
                    
                   
                </div>

              
            </div>

            <div class="px-4 ms-2 me-2">
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
                $file = "pages/$page.php";
                
                if(file_exists($file)){
                    include $file;
                } else {
                    echo "<div class='alert alert-danger'>Halaman tidak ditemukan!</div>";
                }
                ?>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>