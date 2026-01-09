<?php
include "header.php";
include "../config/db.php";

/* KİTAP EKLEME / GÜNCELLEME */
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $title    = trim($_POST['title'] ?? "");
  $author   = trim($_POST['author'] ?? "");
  $year     = trim($_POST['year'] ?? "");
  $category = trim($_POST['category'] ?? "");
  $summary  = trim($_POST['summary'] ?? "");
  $stock    = intval($_POST['stock'] ?? 0);
  $shelf    = trim($_POST['shelf'] ?? "");
  $book_id  = $_POST['book_id'] ?? null;

  if($title==="" || $author===""){
    $error = "Kitap adı ve yazar zorunludur";
  } else {
    if($book_id){
        // GÜNCELLEME
        $stmt = $db->prepare("UPDATE books SET title=?, author=?, category=?, year=?, summary=?, stock=?, shelf=? WHERE id=?");
        $stmt->execute([$title, $author, $category, $year, $summary, $stock, $shelf, $book_id]);
        $success = "Kitap güncellendi";
    } else {
        // YENİ EKLEME
        $stmt = $db->prepare("INSERT INTO books (title, author, category, year, summary, stock, shelf) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$title, $author, $category, $year, $summary, $stock, $shelf]);
        $success = "Kitap başarıyla eklendi";
    }
    header("Location: books_manage.php"); exit;
  }
}

/* SİLME İŞLEMİ */
if(isset($_GET['delete'])){
    $db->prepare("DELETE FROM books WHERE id=?")->execute([$_GET['delete']]);
    header("Location: books_manage.php"); exit;
}

/* DÜZENLEME İÇİN VERİ ÇEKME */
$edit_book = null;
if(isset($_GET['edit'])){
    $st = $db->prepare("SELECT * FROM books WHERE id=?");
    $st->execute([$_GET['edit']]);
    $edit_book = $st->fetch();
}
?>

<div class="admin-card">
  <h3 style="color:white; background:#2c3e50; padding:10px; border-radius:5px;"><?= $edit_book ? '📚 Kitabı Düzenle' : '📚 Yeni Kitap Ekle' ?></h3>
  <form method="POST" style="display:flex; flex-wrap:wrap; gap:10px; padding:15px;">
    <?php if($edit_book): ?><input type="hidden" name="book_id" value="<?= $edit_book['id'] ?>"><?php endif; ?>
    <input name="title" placeholder="Kitap Adı" value="<?= $edit_book['title'] ?? '' ?>" required style="flex:1; min-width:200px;">
    <input name="author" placeholder="Yazar" value="<?= $edit_book['author'] ?? '' ?>" required style="flex:1; min-width:200px;">
    <input name="category" placeholder="Kategori" value="<?= $edit_book['category'] ?? '' ?>" style="width:150px;">
    <input name="year" placeholder="Yıl" value="<?= $edit_book['year'] ?? '' ?>" style="width:80px;">
    <input type="number" name="stock" placeholder="Stok" value="<?= $edit_book['stock'] ?? 0 ?>" style="width:80px;">
    <input name="shelf" placeholder="Raf (Örn: A1)" value="<?= $edit_book['shelf'] ?? '' ?>" style="width:100px;">
    <textarea name="summary" placeholder="Kitap Özeti" style="width:100%; height:60px;"><?= $edit_book['summary'] ?? '' ?></textarea>
    
    <button type="submit" style="background:#27ae60; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; font-weight:bold;">
        <?= $edit_book ? 'Güncelle' : 'Kitap Ekle' ?>
    </button>
    <?php if($edit_book): ?> <a href="books_manage.php" style="padding:10px;">İptal</a> <?php endif; ?>
  </form>
</div>

<div class="admin-card">
  <h3>📖 Kayıtlı Kitaplar</h3>
  <table class="admin-table">
    <tr>
      <th>ID</th><th>Kitap</th><th>Yazar</th><th>Kategori</th><th>Raf</th><th>Stok</th><th>İşlem</th>
    </tr>
    <?php 
    $books = $db->query("SELECT * FROM books ORDER BY id DESC")->fetchAll();
    foreach($books as $b): 
    ?>
    <tr>
      <td><?= $b['id'] ?></td>
      <td><strong><?= htmlspecialchars($b['title']) ?></strong></td>
      <td><?= htmlspecialchars($b['author']) ?></td>
      <td><?= htmlspecialchars($b['category'] ?? '-') ?></td>
      <td style="color:#2980b9; font-weight:bold;"><?= htmlspecialchars($b['shelf'] ?? '-') ?></td>
      <td><?= $b['stock'] ?></td>
      <td>
        <a href="?edit=<?= $b['id'] ?>" style="background:#3498db; color:white; padding:3px 8px; border-radius:3px; text-decoration:none;">Düzenle</a> | 
        <a href="?delete=<?= $b['id'] ?>" style="background:#e74c3c; color:white; padding:3px 8px; border-radius:3px; text-decoration:none;" onclick="return confirm('Emin misiniz?')">Sil</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php include "footer.php"; ?>