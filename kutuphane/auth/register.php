<?php
session_start();
include "../config/db.php";

if(isset($_SESSION['error'])){
  $error = $_SESSION['error'];
  unset($_SESSION['error']);
}

$old = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

if($_POST){
  $name     = trim($_POST['name'] ?? "");
  $email    = trim($_POST['email'] ?? "");
  $password = $_POST['password'] ?? "";
  $school   = trim($_POST['school_no'] ?? "");
  $phone    = trim($_POST['phone'] ?? "");

  // 1. Boş alan kontrolü
  if($name === "" || $email === "" || $password === ""){
    $_SESSION['error'] = "Zorunlu alanları doldurun.";
  }
  // 2. E-posta formatı
  elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION['error'] = "Geçerli bir e-posta giriniz.";
  }
  // 3. Okul no rakam mı?
  elseif($school !== "" && !ctype_digit($school)){
    $_SESSION['error'] = "Okul numarası sadece rakam olmalı.";
  }
  // 4. Telefon 11 hane mi?
  elseif($phone !== "" && (!ctype_digit($phone) || strlen($phone) !== 11)){
    $_SESSION['error'] = "Telefon 11 haneli bir sayı olmalı.";
  }
  // 5. Şifre kuralı
  elseif(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password)){
    $_SESSION['error'] = "Şifre en az 8 karakter, 1 büyük harf ve 1 rakam içermeli.";
  }
  else {
    // 6. Benzersizlik kontrolü (Email ve Okul No)
    $checkEmail = $db->prepare("SELECT id FROM users WHERE email=?");
    $checkEmail->execute([$email]);
    
    $checkSchool = null;
    if($school !== ""){
        $checkSchool = $db->prepare("SELECT id FROM users WHERE school_no=?");
        $checkSchool->execute([$school]);
    }

    if($checkEmail->rowCount() > 0){
      $_SESSION['error'] = "Bu e-posta zaten kayıtlı.";
    } elseif($checkSchool && $checkSchool->rowCount() > 0){
      $_SESSION['error'] = "Bu okul numarası zaten kayıtlı.";
    } else {
      // KAYIT BAŞARILI
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $db->prepare("INSERT INTO users (name,email,password,school_no,phone,role) VALUES (?,?,?,?,?, 'student')")
         ->execute([$name,$email,$hashed,$school,$phone]);

      // Başarı mesajını session'a koy
      $_SESSION['success'] = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
      header("Location: login.php");
      exit;
    }
  }
  $_SESSION['form_data'] = $_POST;
  header("Location: register.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Kayıt Ol</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-bg">
<div class="auth-box">
  <h2>Kayıt Ol</h2>
  <?php if(isset($error)): ?>
    <div style="background:#fceaea; color:#b71c1c; padding:10px; margin-bottom:15px; border-radius:5px;"><?= $error ?></div>
  <?php endif; ?>
  <form method="POST" novalidate>
    <input type="text" name="name" placeholder="Ad Soyad" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
    <input type="text" name="school_no" placeholder="Okul No" value="<?= htmlspecialchars($old['school_no'] ?? '') ?>" pattern="[0-9]*" inputmode="numeric">
    <input type="email" name="email" placeholder="E-posta" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
    <input type="text" name="phone" placeholder="Telefon (05xxxxxxxxx)" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" pattern="[0-9]{11}" maxlength="11" inputmode="numeric">
    <input type="password" name="password" placeholder="Şifre" required>
    <button type="submit">Kayıt Ol</button>
  </form>
  <a href="login.php">Giriş Yap</a>
</div>
</body>
</html>