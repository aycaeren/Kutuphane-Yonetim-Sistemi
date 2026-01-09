<?php
include "header.php"; //
include "../config/db.php";

// Genel Sayılar
$userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$bookCount = $db->query("SELECT COUNT(*) FROM books")->fetchColumn();

// Zaman Bazlı Analizler
$daily_req = $db->query("SELECT COUNT(*) FROM borrow_requests WHERE DATE(request_date) = CURDATE()")->fetchColumn();
$weekly_req = $db->query("SELECT COUNT(*) FROM borrow_requests WHERE request_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

// --- AYLIK ANALİZ EKLEME ---
// Son 30 gün içindeki toplam ödünç taleplerini çeker
$monthly_req = $db->query("SELECT COUNT(*) FROM borrow_requests WHERE request_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();

// En Çok Ödünç Alınan 5 Kitap
$popular_books = $db->query("
    SELECT b.title, COUNT(br.id) as total 
    FROM borrow_requests br 
    JOIN books b ON b.id = br.book_id 
    GROUP BY b.id 
    ORDER BY total DESC 
    LIMIT 5
")->fetchAll();
?>

<div class="admin-card">
  <h3>📊 Sistem Analizleri</h3>
  
  <div style="display:flex; gap:20px; margin-top:20px;">
    <div class="admin-card" style="flex:1; background:#e3f2fd; border: 1px solid #2196f3;">
      <h4>Bugünkü Talepler</h4>
      <p style="font-size:24px;"><strong><?= $daily_req ?></strong></p>
    </div>
    
    <div class="admin-card" style="flex:1; background:#f3e5f5; border: 1px solid #9c27b0;">
      <h4>Haftalık Talepler</h4>
      <p style="font-size:24px;"><strong><?= $weekly_req ?></strong></p>
    </div>

    <div class="admin-card" style="flex:1; background:#e8f5e9; border: 1px solid #4caf50;">
      <h4>Aylık Talepler</h4>
      <p style="font-size:24px;"><strong><?= $monthly_req ?></strong></p>
      <small style="color: #666;">(Son 30 Gün)</small>
    </div>
  </div>

  <div style="margin-top:30px;">
    <h4>🔥 En Çok Ödünç Alınan Kitaplar</h4>
    <table class="admin-table">
        <tr style="background:#f8f9fa;">
            <th>Kitap Adı</th>
            <th>Ödünç Sayısı</th>
        </tr>
        <?php foreach($popular_books as $pb): ?>
        <tr>
            <td><?= htmlspecialchars($pb['title']) ?></td>
            <td><strong><?= $pb['total'] ?> kez</strong></td>
        </tr>
        <?php endforeach; ?>
    </table>
  </div>
</div>

<?php include "footer.php"; ?> 