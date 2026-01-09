<?php
include "header.php"; //
include "../config/db.php";

// Arama ve Filtreleme Parametreleri
$search = trim($_GET['search'] ?? "");
$author = trim($_GET['author'] ?? "");
$year   = trim($_GET['year'] ?? "");

// Dinamik Sorgu Oluşturma
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if ($search !== "") {
    $sql .= " AND (title COLLATE utf8mb4_turkish_ci LIKE ? OR author COLLATE utf8mb4_turkish_ci LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($author !== "") {
    $sql .= " AND author = ?";
    $params[] = $author;
}
if ($year !== "") {
    $sql .= " AND year = ?";
    $params[] = $year;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

// YILLARI VE YAZARLARI TEKRAR GETİRME (Kritik Bölüm)
$authors = $db->query("SELECT DISTINCT author FROM books WHERE author IS NOT NULL ORDER BY author ASC")->fetchAll(PDO::FETCH_COLUMN);
$years   = $db->query("SELECT DISTINCT year FROM books WHERE year IS NOT NULL AND year != '' ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="admin-card">
    <h3>🔍 Kitap Ara ve Filtrele</h3>
    <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Kitap adı veya yazar..." value="<?= htmlspecialchars($search) ?>" style="flex: 2; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        
        <select name="author" style="flex: 1; padding: 10px; border-radius: 5px;">
            <option value="">Tüm Yazarlar</option>
            <?php foreach($authors as $a): ?>
                <option value="<?= htmlspecialchars($a) ?>" <?= $author === $a ? 'selected' : '' ?>><?= htmlspecialchars($a) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="year" style="flex: 1; padding: 10px; border-radius: 5px;">
            <option value="">Tüm Yıllar</option>
            <?php foreach($years as $y): ?>
                <option value="<?= htmlspecialchars($y) ?>" <?= $year === (string)$y ? 'selected' : '' ?>><?= htmlspecialchars($y) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" style="background: #34495e; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">Filtrele</button>
        <a href="books.php" style="padding: 10px; text-decoration: none; color: #666; background: #eee; border-radius: 5px;">Temizle</a>
    </form>
</div>

<div class="admin-card">
  <h3>📚 Kitap Listesi</h3>
  <table class="admin-table">
    <thead>
        <tr>
          <th>Kitap</th>
          <th>Yazar</th>
          <th>Yayın Yılı</th> <th>Raf No</th>
          <th>Stok</th>
          <th>İşlem</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($books) > 0): ?>
            <?php foreach($books as $b): ?>
            <tr>
              <td><strong><?= htmlspecialchars($b['title']) ?></strong></td>
              <td><?= htmlspecialchars($b['author']) ?></td>
              <td><?= htmlspecialchars($b['year'] ?? '-') ?></td> <td style="color: #2980b9; font-weight: bold;"><?= htmlspecialchars($b['shelf'] ?? '-') ?></td>
              <td><?= $b['stock'] ?? 0 ?></td>
              <td>
                <a href="book_detail.php?id=<?= $b['id'] ?>" style="background: #3498db; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">Detay</a> 
                <?php if(($b['stock'] ?? 0) > 0): ?>
                    <a href="my_requests.php?id=<?= $b['id'] ?>" style="background: #27ae60; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-left: 5px;">Ödünç Al</a>
                <?php else: ?>
                    <span style="color: #e74c3c; font-size: 12px; margin-left: 5px;">Stokta Yok</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center; padding: 20px;">Aradığınız kriterlere uygun kitap bulunamadı.</td></tr>
        <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include "footer.php"; ?> //