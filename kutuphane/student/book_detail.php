<?php
include "header.php"; //
include "../config/db.php";

$id = $_GET['id'] ?? 0;
$stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    die("<div class='admin-card'><h3>Kitap bulunamadı!</h3><a href='books.php'>Geri Dön</a></div>");
}
?>

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <a href="books.php" style="text-decoration: none; color:white;">← Listeye Dön</a>
    </div>
    <hr>
    <div style="margin-top: 20px;">
        <p><strong>✍️ Yazar:</strong> <?= htmlspecialchars($book['author']) ?></p>
        <p><strong>📅 Yayın Yılı:</strong> <?= htmlspecialchars($book['year']) ?></p>
        <p><strong>📂 Kategori:</strong> <?= htmlspecialchars($book['category'] ?? "Belirtilmemiş") ?></p>
        
        <p><strong>📍 Raf Bilgisi:</strong> 
            <span style="color: #2980b9; font-weight: bold;">
                <?= htmlspecialchars($book['shelf'] ?? "Bilgi Yok") ?>
            </span>
        </p>
        
        <p><strong>📦 Stok Durumu:</strong> 
            <span style="color: <?= ($book['stock'] ?? 0) > 0 ? 'green' : 'red' ?>;">
                <?= ($book['stock'] ?? 0) > 0 ? "Mevcut (" . $book['stock'] . " adet)" : "Tükendi" ?>
            </span>
        </p>
        
        <div style="margin-top: 30px; padding: 15px; background: #f9f9f9; border-left: 5px solid #3498db;">
            <h4>📖 Kitap Özeti</h4>
            <p style="line-height: 1.6; color: #555;">
                <?= nl2br(htmlspecialchars($book['summary'] ?? "Bu kitap için henüz bir özet eklenmemiş.")) ?>
            </p>
        </div>

        <div style="margin-top: 30px;">
            <?php if(($book['stock'] ?? 0) > 0): ?>
                <a href="my_requests.php?id=<?= $book['id'] ?>" class="btn-borrow" style="padding: 10px 25px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">Hemen Ödünç Al</a>
            <?php else: ?>
                <button disabled style="padding: 10px 25px; background: #ccc; color: white; border: none; border-radius: 5px;">Stokta Yok</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "footer.php"; ?> //