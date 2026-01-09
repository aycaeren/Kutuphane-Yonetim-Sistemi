<?php
session_start();
include "../config/db.php";

/* 🔴 HATA MESAJI VARSA AL VE SİL */
if(isset($_SESSION['error'])){
  $error = $_SESSION['error'];
  unset($_SESSION['error']);
}

/* 🟢 BAŞARI MESAJI (Kayıt Olunca Gelen) VARSA AL VE SİL */
if(isset($_SESSION['success'])){
  $success = $_SESSION['success'];
  unset($_SESSION['success']);
}

if($_POST){
  $email = trim($_POST['email'] ?? "");
  $password = $_POST['password'] ?? "";

  $user = $db->prepare("SELECT * FROM users WHERE email=?");
  $user->execute([$email]);
  $u = $user->fetch();

  if($u && password_verify($password, $u['password'])){
    $_SESSION['user'] = $u;

    // Role göre yönlendirme
    if($u['role'] == "student"){
      header("Location: ../student/dashboard.php");
      exit;
    }
    if($u['role'] == "staff"){
      header("Location: ../staff/dashboard.php");
      exit;
    }
    if($u['role'] == "admin"){
      header("Location: ../admin/dashboard.php");
      exit;
    }

  } else {
    $_SESSION['error'] = "E-posta veya şifre hatalı";
    header("Location: login.php");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Giriş Yap</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="auth-bg">

<div class="auth-box">
  <h2>Giriş Yap</h2>

  <?php if(isset($success)): ?>
    <div style="background:#d4edda; color:#155724; padding:12px; margin-bottom:15px; border-radius:5px; border:1px solid #c3e6cb; font-size:14px; text-align:center;">
      <?= $success ?>
    </div>
  <?php endif; ?>

  <?php if(isset($error)): ?>
    <div style="background:#f8d7da; color:#4ca1af; padding:12px; margin-bottom:15px; border-radius:5px; border:1px solid #f5c6cb; font-size:14px; text-align:center;">
      <?= $error ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <input type="email" name="email" 
           placeholder="E-posta" 
           autocomplete="email"
           required>

    <input type="password" name="password" 
           placeholder="Şifre" 
           required>

    <button type="submit">Giriş Yap</button>
  </form>

  <div style="margin-top: 15px; text-align:center;">
    <a href="register.php">Henüz hesabınız yok mu? Kayıt Ol</a>
  </div>
</div>

</body>
</html>