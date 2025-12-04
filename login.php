<?php
session_start();
include 'config/database.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $data['id_user'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama'] = $data['nama_lengkap'];
        header("Location: index.php");
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GDSS Wisata</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/image/logo.png">

    <style>
        body, input, button, .form-control, h1, h2, h3, h4, h5, p, span, ul, li {
            font-family: 'Poppins', sans-serif !important;
        }

        body.login-body {
            background: linear-gradient(135deg, #1a1464 0%, #252440 100%); 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .card-login {
            width: 100%;
            max-width: 400px;
            border-radius: 16px;
            background-color: #ffffff;
            border: none;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            padding-bottom: 0 !important;
            overflow: hidden;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        /* --- CSS BARU UNTUK JUDUL --- */
        .title-gradient-thin {
            /* Font weight 100 = Sangat Tipis */
            font-weight: 300 !important;
            /* Gradasi Ungu ke Biru */
            background: linear-gradient(90deg, #9333ea 0%, #2563eb 100%);
            /* Memotong background ke teks */
            -webkit-background-clip: text;
            background-clip: text;
            /* Membuat fill text transparan */
            -webkit-text-fill-color: transparent;
            color: transparent; 
            font-size: 1.35rem; /* Sedikit diperbesar agar terbacanya enak meski tipis */
        }
        /* --------------------------- */

        .form-control {
            background-color: #f7f7f9;
            border: 1px solid #eee;
            padding: 12px 16px;
            font-size: 14px;
            border-radius: 8px;
            color: #333;
            font-weight: 400; /* Input text normal weight */
        }
        
        .form-control:focus {
            background-color: #fff;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #1e1b4b;
            margin-bottom: 6px;
        }

        .btn-gradient {
            background: linear-gradient(90deg, #9333ea 0%, #2563eb 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            transition: opacity 0.3s;
        }
        
        .btn-gradient:hover {
            opacity: 0.9;
            color: white;
        }

        .demo-box {
            background-color: #f0f9ff;
            border-radius: 12px;
            padding: 15px;
            margin-top: 25px;
            border: 1px dashed #bae6fd;
            text-align: left;
        }
        
        .demo-list {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 0;
            padding-left: 15px;
        }
        
        .logo-shadow {
            filter: drop-shadow(0 4px 6px rgba(124, 58, 237, 0.3));
        }
    </style>
</head>
<body class="login-body">

    <div class="card p-4 card-login">
        <div class="card-body px-2">
            <div class="login-header">
                <img src="assets/image/logo.png" width="60" class="mb-3 logo-shadow" alt="Logo">
                
                <h5 class="title-gradient-thin mb-2">GDSS Wisata Kota Batu</h5>
                
                <p class="text-muted" style="font-size: 12px; font-weight: 300;">
                    Sistem Pendukung Keputusan Kelompok<br>Pemilihan Destinasi Wisata Unggulan
                </p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger text-center py-2 small border-0 mb-3" style="font-size: 12px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" name="login" class="btn btn-gradient w-100 py-2">
                    Masuk
                </button>
            </form>
        
        </div>
    </div>

</body>
</html>