<?php
session_start();
include "../config/db.php";

// 1. Giriş yapılmış mı?
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit;
}

// 2. Giriş yapan kişi personel mi?
if($_SESSION['user']['role'] !== 'staff'){
    echo "Bu sayfaya erişim yetkiniz yok. Sadece personel girebilir.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kitap Listesi - Personel Paneli</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .container { width: 90%; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; color: #333; }
        tr:hover { background-color: #f9f9f9; }
        .btn { padding: 5px 10px; border-radius: 3px; text-decoration: none; font-size: 13px; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; margin-left: 5px; }
        .add-box { margin-bottom: 20px; text-align: right; }
        .add-btn { background: #27ae60; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>Kitap Yönetimi</h2>
    <p>Sistemde kayıtlı olan tüm kitapları buradan görebilir ve yönetebilirsiniz.</p>

    <div class="add-box">
        <a href="add_book.php" class="add-btn">+ Yeni Kitap Ekle</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kitap Adı</th>
                <th>Yazar</th>
                <th>ISBN</th>
                <th>Stok</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($books) > 0): ?>
                <?php foreach($books as $book): ?>
                <tr>
                    <td><?= $book['id'] ?></td>
                    <td><strong><?= htmlspecialchars($book['title']) ?></strong></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['isbn'] ?? '-') ?></td>
                    <td><?= $book['stock_count'] ?></td>
                    <td>
                        <a href="edit_book.php?id=<?= $book['id'] ?>" class="btn btn-edit">Düzenle</a>
                        <a href="delete_book.php?id=<?= $book['id'] ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('Bu kitabı silmek istediğinize emin misiniz?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Henüz hiç kitap eklenmemiş.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>