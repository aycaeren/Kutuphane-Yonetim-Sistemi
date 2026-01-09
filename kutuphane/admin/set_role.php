<?php
session_start();
include "../config/db.php";

// Güvenlik: Sadece admin bu dosyayı çalıştırabilir
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != "admin"){
    die("Yetkisiz erişim!");
}

if(isset($_GET['id']) && isset($_GET['role'])){
    $id = $_GET['id'];
    $role = $_GET['role'];
    $allowed_roles = ['student', 'staff', 'admin'];

    if(in_array($role, $allowed_roles)){
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $id]);
    }
}

header("Location: users_manage.php");
exit;