
<?php
session_start();

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Akıllı Kütüphane Yönetim Sistemi</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<!-- ÜST ALAN -->
<div class="index-hero">
  <h1>Akıllı Kütüphane Yönetim Sistemi</h1>
  <p>Kitap ödünç alma, takip ve yönetim işlemleri</p>

  <div class="index-actions">
    <a href="auth/login.php">Giriş Yap</a>
    <a href="auth/register.php">Kayıt Ol</a>
  </div>
</div>

<!-- ALT BİLGİ -->
<div class="index-info">
  <div class="card">
    <h3>📚 Kitap Yönetimi</h3>
    <p>Kitapları görüntüle ve ödünç talebi oluştur</p>
  </div>

  <div class="card">
    <h3>👤 Kullanıcı Sistemi</h3>
    <p>Öğrenci ve personel rolleri ile güvenli giriş</p>
  </div>

  <div class="card">
    <h3>🔄 Ödünç Takibi</h3>
    <p>Beklemede, onaylandı ve iade süreçleri</p>
  </div>
</div>

</body>
</html>
