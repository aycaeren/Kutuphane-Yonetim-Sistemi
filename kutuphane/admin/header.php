<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SADECE ADMIN GİREBİLİR
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "admin"){
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Yönetici Paneli</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-container">
    <div class="admin-header">
        <h1>Yönetici Paneli</h1>
    </div>
    <div class="admin-menu">
        <a href="dashboard.php">Anasayfa</a>
        <a href="books_manage.php">Kitap Yönetimi</a>
        <a href="users_manage.php">Kullanıcı Yönetimi</a>
        <a href="reports.php">Raporlar</a>
        <a href="../auth/logout.php">Çıkış</a>
    </div>