<?php
if(!isset($_SESSION)){
  session_start();
}

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != "student"){
  die("Yetkisiz erişim");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Öğrenci Paneli</title>

<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="admin-container">

<div class="admin-header">
  <h1>Öğrenci Paneli</h1>
</div>

<div class="admin-menu">
  <a href="dashboard.php">Anasayfa</a>
  <a href="books.php">Kitaplar</a>
  <a href="my_requests.php">Ödünç Taleplerim</a>
  <a href="../auth/logout.php">Çıkış</a>
</div>
