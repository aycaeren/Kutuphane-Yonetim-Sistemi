<?php
// Oturum kontrolünü en güvenli şekilde başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kütüphane Paneli</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-container">
    <div class="admin-header">
        <h1>
            <?php
            // Giriş yapan kullanıcının rolüne göre başlığı yazdır
            if($_SESSION['user']['role'] == "student") echo "Öğrenci Paneli";
            elseif($_SESSION['user']['role'] == "staff") echo "Personel Paneli";
            else echo "Yönetim Paneli";
            ?>
        </h1>
    </div>

    <div class="admin-menu">
        <?php if($_SESSION['user']['role'] == "staff"): ?>
          
            <a href="dashboard.php" class="btn-menu">Ödünç Talepleri</a>
            
        <?php elseif($_SESSION['user']['role'] == "student"): ?>
            <a href="dashboard.php" class="btn-menu">Anasayfa</a>
            <a href="books.php" class="btn-menu">Kitaplar</a>
            <a href="requests.php" class="btn-menu">Ödünç Taleplerim</a>
            
        <?php endif; ?>
        
        <a href="../auth/logout.php" class="btn-menu" style="background-color: #3498db; color: white;">Çıkış</a>
    </div>